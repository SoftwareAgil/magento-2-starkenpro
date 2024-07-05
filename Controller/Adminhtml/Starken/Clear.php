<?php
/**
 * Admin Clear Log controller
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Controller\Adminhtml\Starken;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use SoftwareAgil\StarkenPro\Model\ResourceModel\Log\Collection;
use SoftwareAgil\StarkenPro\Model\ResourceModel\Log\CollectionFactory;

/**
 * Class Clear
 * @package SoftwareAgil\StarkenPro\Controller\Adminhtml\Starken
 */
class Clear extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SoftwareAgil_StarkenPro::log';

    /**
     * @var CollectionFactory
     */
    protected $collectionLog;

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionLog
     * @param Context $context
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionLog
    ) {
        $this->collectionLog = $collectionLog;

        parent::__construct($context);
    }

    /**
     * Clear Emails Log
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        /** @var Collection $collection */
        $collection = $this->collectionLog->create();
        try {
            $collection->clearLog();
            $this->messageManager->addSuccess(__('Records older than a month have been removed'));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong.'));
        }

        return $resultRedirect->setPath('*/*/log');
    }
}
