<?php

namespace Mancini\ProductConsole\Block\Adminhtml\Import\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\ImportExport\Model\Import;

/**
 * Class Form
 */
class Form extends Generic
{
    /**
     * Basic import model
     *
     * @var Import
     */
    protected $_importModel;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Import $importModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Import $importModel,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_importModel = $importModel;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('product_console/import/process'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );

        // fieldset for file uploading
        $fieldsets['upload'] = $form->addFieldset(
            'upload_file_fieldset',
            ['legend' => __('File to Import')]
        );
        $fieldsets['upload']->addField(
            Import::FIELD_NAME_SOURCE_FILE,
            'file',
            [
                'name' => Import::FIELD_NAME_SOURCE_FILE,
                'label' => __('Select File to Import'),
                'title' => __('Select File to Import'),
                'required' => true,
                'class' => 'input-file'
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get download sample file html
     *
     * @return string
     */
    protected function getDownloadSampleFileHtml()
    {
        $html = '<span id="sample-file-span" class="no-display"><a id="sample-file-link" href="#">'
            . __('Download Sample File')
            . '</a></span>';
        return $html;
    }
}
