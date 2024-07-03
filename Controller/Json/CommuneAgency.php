<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SoftwareAgil\StarkenPro\Controller\Json;

class CommuneAgency extends \Magento\Framework\App\Action\Action
{
    /**
     * Return JSON-encoded array of commune agencies
     *
     * @return string
     */
    public function execute()
    {
        $arrRes = [];

        $communeId = $this->getRequest()->getParam('parent');
        if (!empty($communeId)) {
            $arrAgencies = $this->_objectManager->create(
                \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\Collection::class
            )->addCommuneSpFilter(
                $communeId
            )->load()->toOptionArray();

            if (!empty($arrAgencies)) {
                foreach ($arrAgencies as $agency) {
                    $arrRes[] = $agency;
                }
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($arrRes)
        );
    }
}
