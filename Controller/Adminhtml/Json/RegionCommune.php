<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SoftwareAgil\StarkenPro\Controller\Adminhtml\Json;

class RegionCommune extends \Magento\Backend\App\Action
{
    /**
     * Return JSON-encoded array of commune agencies
     *
     * @return string
     */
    public function execute()
    {
        $arrRes = [];

        $regionId = $this->getRequest()->getParam('parent');
        if (!empty($regionId)) {
            $arrCommunes = $this->_objectManager->create(
                \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection::class
            )->addRegionFilter(
                $regionId
            )->load()->toOptionArray();

            if (!empty($arrCommunes)) {
                foreach ($arrCommunes as $commune) {
                    $arrRes[] = $commune;
                }
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($arrRes)
        );
    }
}
