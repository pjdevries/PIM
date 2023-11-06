<?php
/**
 * @package    Kwekfestijn
 *
 * @copyright  Copyright (C) 2015 HKweb. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace HKweb\Component\Kwekfestijn\Site\Controller;

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\QueryInterface;
use Pim\Api\Exception\ApiDisabledException;
use Pim\Api\Exception\InsecureRequestException;
use Pim\Api\Exception\InvalidRequestMethodException;
use Pim\Api\Exception\UnauthorizedException;

/**
 * Kwekfestijn Component Controller
 *
 * @since  1.5
 */
class ItemsApiController extends BaseController
{
    /**
     * @var bool
     */
    protected bool $apiEnabled = false;

    /**
     * @var string
     */
    protected string $apiKey = '';

    public function __construct()
    {
        parent::__construct();

        $this->init();
    }

    /**
     * @return void
     */
    protected function init(): void
    {
        // Load component language strings.
        $language = $this->app->getLanguage();
        $language->load('com_pim', JPATH_COMPONENT);

        // Obtain API settings from plugin.
        $plugin = PluginHelper::getPlugin('system', 'pim');

        // Unfortunately the return valkue from PluginHelper is not always the same,
        // but can be q single object or an array.
        if (is_array($plugin)) {
            $plugin = count($plugin) ? array_shift($plugin) : null;
        }

        if (!($plugin && is_object($plugin))) {
            return;
        }

        $params = json_decode($plugin->params);

        // Set API state and key in this object, to be accessed later on when an actual request is being made.
        $this->apiEnabled = (bool)$params->api_enabled;
        $this->apiKey = $params->api_key;
    }

    /**
     * @param $cachable
     * @param $urlparams
     * @return void
     */
    public function display($cachable = false, $urlparams = [])
    {
        $this->sendResponse([], 'Onbekende taak: ' . $this->app->getInput()->getCmd('task'), 404);
    }

    /**
     * @param QueryInterface $query
     * @return void
     */
    private function sendQueryResult(QueryInterface $query): void
    {
        try {
            $this->db->setQuery($query);
            $results = $this->db->loadObjectList();

            if (!$results) {
                $this->sendResponse([], 'DB gave no results', 404);
            }

            $this->sendResponse($results, '', 200);
        } catch (\mysqli_sql_exception $e) {
            $this->sendResponse([], $e->getMessage() . ' (MySQL ' . $e->getCode() . ')', 500);
        } catch (\Exception $e) {
            $this->sendResponse([], $e->getMessage(), $e->getCode(), 500);
        }
    }

    /**
     * @param string $task
     * @param string $requestMethod
     * @return void
     */
    public function checkRequest(string $task, string $requestMethod): void
    {
        // Check if API is enabled.
        if (!$this->apiEnabled) {
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

        if (!$this->checkAuthorisation()) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @return bool
     */
    private function checkAuthorisation(): bool
    {
        $headers = getallheaders();

        // Must match API key.
        if (!($authorization = $headers['Api-Authorization'] ?? '')) {
            return false;
        }

        $parts = explode(' ', $authorization);

        if (!count($parts) === 2 && ($scheme = strtoupper($parts[0])) === 'BASIC') {
            return false;
        }

        $token = base64_decode($parts[1]);

        return !is_null($this->apiKey) && $token === $this->apiKey;
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
