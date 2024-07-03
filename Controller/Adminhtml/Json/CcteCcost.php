<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SoftwareAgil\StarkenPro\Controller\Adminhtml\Json;

class CcteCcost extends \Magento\Backend\App\Action
{
    /**
     * Return JSON-encoded array of ccte cost centers
     *
     * @return string
     */
    public function execute()
    {
        $arrRes = [];

        $ccteId = $this->getRequest()->getParam('parent');
        if (!empty($ccteId)) {
            $arrCctes = $this->_objectManager->create(
                \SoftwareAgil\StarkenPro\Model\ResourceModel\Account\Collection::class
            )->addCcteFilter(
                $ccteId
            )->load()->toOptionArray();

            if (!empty($arrCctes)) {
                foreach ($arrCctes as $ccte) {
                    $arrRes[] = $ccte;
                }
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($arrRes)
        );
    }
}
