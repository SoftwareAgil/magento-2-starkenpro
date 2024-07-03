<?php
/**
 * Rut source type
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
class Rut extends Generic
{
    /**
     * Source field code
     *
     * @var string
     */
    protected $code = 'rut';

    /**
     * @var \Magento\Directory\Model\AccountFactory
     */
    private $_accountFactory;

    /**
     * @param \Magento\Directory\Model\AccountFactory $accountFactory
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
            ->columns('rut')
            ->group('rut');
        foreach($accountCollection as $rut) {
            $options[] = ['value' => $rut->getRut(), 'label' => $rut->getRut() . '-' . $this->getRutDv($rut->getRut())];
        }

        return $options;
    }

    /**
     * Get RUT Verification Digit
     * @param string $r
     */
    function getRutDv($r){
        $s = 1;
        for($m = 0; $r != 0; $r /= 10) {
            $s = ($s + $r % 10 * (9 - $m++ % 6)) % 11;
        }

        return chr($s ? $s + 47 : 75);
    }
}
