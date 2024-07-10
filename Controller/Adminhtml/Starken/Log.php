<?php
/**
 * Admin Log controller
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Controller\Adminhtml\Starken;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Log
 * @package SoftwareAgil\StarkenPro\Controller\Adminhtml\Starken
 */
class Log extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SoftwareAgil_StarkenPro::log';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SoftwareAgil_StarkenPro::log');
        $resultPage->getConfig()->getTitle()->prepend(__('Starken') . " - " . __('API Calls Log'));

        //Add bread crumb
        $resultPage->addBreadcrumb(__('Starken'), __('Starken'));
        $resultPage->addBreadcrumb(__('API Calls Log'), __('API Calls Log'));

        return $resultPage;
    }
}
