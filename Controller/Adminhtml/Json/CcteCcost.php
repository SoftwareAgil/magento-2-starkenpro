<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SoftwareAgil\StarkenPro\Controller\Adminhtml\Json;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;
use SoftwareAgil\StarkenPro\Model\ResourceModel\Account\Collection;

class CcteCcost implements ActionInterface, HttpPostActionInterface, HttpGetActionInterface
{
    public function __construct(
        protected PageFactory $resultPageFactory,
        protected Json $jsonHelper,
        protected RequestInterface $_request,
        protected ResponseInterface $_response,
        protected Collection $collection
    ) {
    }

    /**
     * Return JSON-encoded array of ccte cost centers
     *
     * @return string
     */
    public function execute()
    {
        $arrRes = [];

        $ccteId = $this->_request->getParam('parent');
        if (!empty($ccteId)) {
            $arrCctes = $this->collection->addCcteFilter(
                $ccteId
            )->load()->toOptionArray();

            if (!empty($arrCctes)) {
                foreach ($arrCctes as $ccte) {
                    $arrRes[] = $ccte;
                }
            }
        }
        
        return $this->_response->representJson(
            $this->jsonHelper->serialize($arrRes)
        );
    }
}
