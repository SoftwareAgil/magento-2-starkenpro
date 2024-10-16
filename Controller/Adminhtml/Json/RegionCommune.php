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
use SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection;

class RegionCommune implements ActionInterface, HttpPostActionInterface,HttpGetActionInterface
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
     * Return JSON-encoded array of commune agencies
     *
     * @return string
     */
    public function execute()
    {
        $arrRes = [];

        $regionId = $this->_request->getParam('parent');
        if (!empty($regionId)) {
            $arrCommunes = $this->collection->addRegionFilter(
                $regionId
            )->load()->toOptionArray();

            if (!empty($arrCommunes)) {
                foreach ($arrCommunes as $commune) {
                    $arrRes[] = $commune;
                }
            }
        }

        return $this->_response->representJson(
            $this->jsonHelper->serialize($arrRes)
        );
    }
}
