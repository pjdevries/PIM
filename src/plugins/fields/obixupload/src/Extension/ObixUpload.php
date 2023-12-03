<?php
/**
 * @package     ObixUploads
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Joomla\Plugin\Fields\ObixUpload\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin;
use Joomla\Event\Event;
use Joomla\Event\Priority;
use Joomla\Event\SubscriberInterface;
use Obix\Filesystem\Upload\Handler;
use Obix\Filesystem\Upload\Prerequisites;


/**
 * Fields Text Plugin
 *
 * @since  3.7.0
 */
final class ObixUpload extends FieldsPlugin implements SubscriberInterface
{
    public function __construct(&$subject, $config = [])
    {
        parent::__construct($subject, $config);

        FormHelper::addFieldPrefix('Obix\Form\Field');

        /** @var WebAssetManager $wam */
        $wam = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wam
            ->registerAndUseScript('obix.upload.js', 'obix/upload.min.js')
            ->registerAndUseStyle('obix.upload.css', 'obix/upload.min.css');
    }

    /**
     * Returns an array of events this subscriber will listen to.
     * The array keys are event names and the value can be:
     * The method name to call (priority defaults to 0)
     * An array composed of the method name to call and the priority
     *
     * For instance:
     *   ['eventName' => 'methodName']
     *   ['eventName' => ['methodName', $priority]]
     *
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        // Register event handlers.
        return [
            'onCustomFieldsGetTypes' => ['handleCustomFieldsGetTypes', Priority::NORMAL],
            'onContentPrepareForm' => ['handleContentPrepareForm', Priority::BELOW_NORMAL],
            'onCustomFieldsPrepareField' => ['handleCustomFieldsPrepareField', Priority::NORMAL],
            'onCustomFieldsPrepareDom' => ['handleCustomFieldsPrepareDom', Priority::NORMAL],
            'onContentBeforeSave' => ['handleContentBeforeSave', Priority::NORMAL],
            'onContentNormaliseRequestData' => ['handleContentNormaliseRequestData', Priority::NORMAL],
        ];
    }

    public function handleCustomFieldsGetTypes(Event $event): void
    {
        $result = array_values($event->getArgument('result') ?? []);

        $event->setArgument('result', [...$result, parent::onCustomFieldsGetTypes()]);
    }

    public function handleContentPrepareForm(Event $event): void
    {
        /**
         * @var   Form $form
         * @var   object $data
         */
        [$form, $data] = array_values($event->getArguments());

        parent::onContentPrepareForm($form, $data);
    }

    public function handleCustomFieldsPrepareField(Event $event): void
    {
        /**
         * @var   string $context The context.
         * @var   \stdclass $item The item.
         * @var   \stdclass $field The field.
         */
        [$context, $item, $field] = array_values($event->getArguments());

        parent::onCustomFieldsPrepareField($context, $item, $field);
    }

    public function handleCustomFieldsPrepareDom(Event $event)
    {
        [$field, $parent, $form] = array_values($event->getArguments());

        $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

        if ($field->type !== 'obixupload' || !$fieldNode) {
            return $fieldNode;
        }

        $fieldParams = $this->params->merge($field->fieldparams);

        $maxUploadSize = $fieldParams->get('maxUploadSize') ?: ($this->params->get('maxUploadSize') ?: '2M');
        $destDir = $fieldParams->get('destDir') ?: ($this->params->get('destDir') ?: 'images');

        // The 'name' and 'label' attributes are taken from the Name and Label fields
        // when a user creates a new instance of the custom field.
        $fieldNode->setAttribute('type', 'Obixupload');
        $fieldNode->setAttribute('published', $field->fieldparams->get('published', '1'));
        $fieldNode->setAttribute('client_id', $field->fieldparams->get('client_id', '0'));
        $fieldNode->setAttribute('language', $field->fieldparams->get('language', '*'));

        $fieldNode->setAttribute('maxuploadsize', $maxUploadSize);
        $fieldNode->setAttribute('destdir', $destDir);
//        $fieldNode->setAttribute('fileFilter', $fieldParams->get('fileFilter'));
//        $fieldNode->setAttribute('accept', $fieldParams->get('accept'));

        return $fieldNode;
    }

    public function handleContentNormaliseRequestData(Event $event): void
    {
        [$context, $data, $form] = array_values($event->getArguments());

        $allFiles = $this->app->getInput()->files->get('jform', [], 'RAW');

        // Make sure we have a com_fields key exists in the uploaded files list.
        if (!is_array(($data?->com_fields ?? null)) || !is_array($allFiles['com_fields'] ?? null)) {
            return;
        }

        // Process all uploaded files.
        if (count($allFiles['com_fields'])) {
            $handlers = Handler::handle($allFiles['com_fields'], $form);

            foreach ($handlers as $fieldName => $handler) {
                $uploadedFiles = $handler->getSuccesful();

                if (!count($uploadedFiles)) {
                    continue;
                }

                $oldFilesData = json_decode(($data->com_fields[$fieldName] ?? null) ?: '{}', true);
                $maxFileId = array_reduce($oldFilesData, fn(int $id, array $fileData) => max($id, $fileData['id']), 0);
                $addionalFilesData = array_map(function (array $file) use (&$maxFileId) {
                    return [
                        'id' => ++$maxFileId,
                        'name' => $file['name'],
                        'full_path' => $file['full_path'] ?? '',
                        'dest_path' => $file['dest_path']
                    ];
                }, $uploadedFiles);

                $newFilesData = [
                    ...$oldFilesData,
                    ...$addionalFilesData
                ];

                $data->com_fields[$fieldName] = json_encode($newFilesData);
            }
        }
    }

    public function handleContentBeforeSave(Event $event): void
    {
        /**
         * @var string $context
         * @var Table $item
         * @var bool $isNew
         * @var array $data
         */
        [$context, $table, $isNew, $data] = array_values($event->getArguments());

        return;
    }

    public function getUploadPrerequisites(array $fieldNames, Form $form): array
    {
        $prerequisites = [];

        $uploadFieldSpecsByFieldName = array_reduce($form->getFieldset(), function (array $carry, FormField $field) {
            if (strtolower($field->getAttribute('type')) === $this->_name) {
                $carry[$field->getProperty('fieldname')] = [
                    'maxUploadSize' => $field->getAttribute('maxUploadSize'),
                    'destDir' => $field->getAttribute('destDir')
                ];
            }

            return $carry;
        }, []);

        foreach ($fieldNames as $fieldName) {
            $fieldSpecs = $uploadFieldSpecsByFieldName[$fieldName];
            $prerequisites[$fieldName] = new Prerequisites($fieldSpecs['destDir'], $fieldSpecs['maxUploadSize']);
        }

        return $prerequisites;
    }
}
