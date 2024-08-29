<?php
/**
 * Cost Center source type
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\Source;

use SoftwareAgil\StarkenPro\Model\AccountFactory;

/**
 * Method source
 */
class CostCenter extends Generic
{
    /**
     * Source field code
     *
     * @var string
     */
    protected $code = 'rut';

    /**
     * @var AccountFactory
     */
    private $_accountFactory;

    /**
     * @param AccountFactory $accountFactory
     */
    public function __construct(
        AccountFactory $accountFactory
    )
    {
        $this->_accountFactory = $accountFactory;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $accountCollection = $this->_accountFactory->create()
            ->getCollection();
        $accountCollection->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(['cost_center', 'cost_center_name', 'checking_account', 'checking_account_dv'])
            ->group(['cost_center', 'cost_center_name', 'checking_account', 'checking_account_dv']);
        foreach($accountCollection as $cc) {
            $options[] = ['value' => $cc->getCostCenter(), 'label' => $cc->getCostCenterName() . '(' . __('Checking Account') . ': ' . $cc->getCheckingAccount().'-'.$cc->getCheckingAccountDv() . ')'];
        }

        return $options;
    }
}
