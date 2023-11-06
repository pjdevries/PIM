<?php
/**
 * @package    Kwekfestijn
 *
 * @copyright  Copyright (C) 2015 HKweb. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Pim\Component\Pim\Site\Controller;

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Uri\Uri;
use Pim\Api\Exception\ApiDisabledException;
use Pim\Api\Exception\InsecureRequestException;
use Pim\Api\Exception\InvalidRequestMethodException;
use Pim\Api\Exception\UnauthorizedException;
use Pim\Component\Pim\Site\Model\ItemsModel;

/**
 * Kwekfestijn Component Controller
 *
 * @since  1.5
 */
class ItemsApiController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // Load component language strings.
        $language = $this->app->getLanguage();
        $language->load('com_pim', JPATH_COMPONENT);
    }

    /**
     * @return void
     */
    protected function init(): void
    {
    }

    public static function getApiState(): ?object
    {
        $state = null;

        if ($state) {
            return $state;
        }

        // Obtain API settings from plugin.
        $plugin = PluginHelper::getPlugin('system', 'pim');

        // Unfortunately the return valkue from PluginHelper is not always the same,
        // but can be q single object or an array.
        if (is_array($plugin)) {
            $plugin = count($plugin) ? array_shift($plugin) : null;
        }

        if (!($plugin && is_object($plugin))) {
            $state = (object)[
                'enabled' => false,
                'key' => ''
            ];

            return $state;
        }

        $params = json_decode($plugin->params);

        // Set API state and key in this object, to be accessed later on when an actual request is being made.
        $state = (object)[
            'enabled' => (bool)$params?->api_enabled ?? false,
            'key' => $params?->api_key ?? ''
        ];

        return $state;
    }

    /**
     * @param $cachable
     * @param $urlparams
     * @return void
     */
    public function display($cachable = false, $urlparams = [])
    {
        $this->sendResponse([], Text::sprintf('COM_PIM_API_EXCEPTION_UNKNOWN_REQUEST_TASK', $this->app->getInput()->getCmd('task')), 404);
    }

    public function getItems(): void
    {
        try {
            $this->checkRequest('getItems', 'GET');

            /** @var ItemsModel $model */
            $model = $this->app->bootComponent('com_pim')->getMVCFactory()->createModel('Items', 'Site');

            $this->sendResponse($model->getItems());
        } catch (\Exception $e) {
            $this->sendResponse([], $e->getMessage(), $e->getCode());
        }

    }

    public function postItems(): void
    {
        try {
            $this->checkRequest('postItems', 'POST');

            $filters = [];

            if ($orderId = $this->app->input->getInt('orderId', 0)) {
                $filters['orderId'] = $orderId;
            }

            if ($createdDate = $this->app->input->getString('fromCreatedDate', '')) {
                $filters['fromCreatedDate'] = $createdDate;
            }

            /** @var ItemsModel $model */
            $model = $this->app->bootComponent('com_pim')->getMVCFactory()->createModel('Item', 'Site');

            $this->sendResponse(iterator_to_array($model->getOrders($filters)));
        } catch (\Exception $e) {
            $this->sendResponse([], $e->getMessage(), $e->getCode());
        }

    }

    /**
     * @param string $task
     * @param string $requestMethod
     * @return void
     */
    public function checkRequest(string $task, string $requestMethod): void
    {
        $apiState = self::getApiState();

        // Check if API is enabled.
        if (!$apiState->enabled) {
            throw new ApiDisabledException();
        }

        // Check if request was made over a secure connection.
        if (Uri::getInstance()->getScheme() !== 'https') {
            throw new InsecureRequestException();
        }

        // Check if task is available for request method.
        if (strtoupper($requestMethod) !== $_SERVER['REQUEST_METHOD']) {
            throw new InvalidRequestMethodException(
                Text::sprintf('COM_PIM_API_EXCEPTION_INVALID_REQUEST_TASK', $task, $requestMethod)
            );
        }

        if (!$this->checkAuthorisation($apiState->key)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @return bool
     */
    private function checkAuthorisation(string $key): bool
    {
        $headers = getallheaders();

        // Must match API key.
        if (!($authorization = $headers['Api-Authorization'] ?? '')) {
            return false;
        }

        $token = base64_decode($authorization);

        return !is_null($key) && $token === $key;
    }

    /**
     * @param array $data
     * @param string|null $message
     * @param int $status
     * @return void
     */
    protected function sendResponse(array $data, string $message = null, int $status = 200)
    {
        $this->app->setHeader('status', $status);
        $this->app->setHeader('Access-Control-Allow-Origin', '*');
        $this->app->setHeader('Content-Type', 'application/json');
        $this->app->sendHeaders();

        echo new JsonResponse($data, $message, $status !== 200);

        $this->app->close();
    }
}
