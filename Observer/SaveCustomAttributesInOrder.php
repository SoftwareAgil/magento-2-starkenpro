<?php
/**
 * Admin API validations observer
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Observer;

use SoftwareAgil\StarkenPro\Helper\Data;
use SoftwareAgil\StarkenPro\Model\ApiConnector\Client;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Directory\Model\Country;
use Magento\Directory\Model\RegionFactory;
use SoftwareAgil\StarkenPro\Model\CommuneFactory;
use SoftwareAgil\StarkenPro\Model\AgencyFactory;

/**
 * Save extension attributes when an order is placed.
 */
class SaveCustomAttributesInOrder implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        if ($quote->getBillingAddress()) {
            $quoteBillingAddress = $quote->getBillingAddress();
            if ($quote->getBillingAddress()->getExtensionAttributes()) {
                $quoteBillingAddress = $quote->getBillingAddress()->getExtensionAttributes();
            }
            $order->getBillingAddress()->setCommuneId($quoteBillingAddress->getCommuneId());
            $order->getBillingAddress()->setCommune($quoteBillingAddress->getCommune());
            $order->getBillingAddress()->setRut($quoteBillingAddress->getRut());
            $order->getBillingAddress()->setAgencyId($quoteBillingAddress->getAgencyId());
            if (!$quote->getShippingAddress()) {
                $order->getShippingAddress()->setCommuneId($quoteBillingAddress->getCommuneId());
                $order->getShippingAddress()->setCommune($quoteBillingAddress->getCommune());
                $order->getBillingAddress()->setCommuneId($quoteBillingAddress->getCommuneId());
                $order->getBillingAddress()->setCommune($quoteBillingAddress->getCommune());
                $order->getBillingAddress()->setRut($quoteBillingAddress->getRut());
                $order->getBillingAddress()->setAgencyId($quoteBillingAddress->getAgencyId());
            }
        }
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            $order->getShippingAddress()->setCommuneId($quote->getShippingAddress()->getCommuneId());
            $order->getShippingAddress()->setCommune($quote->getShippingAddress()->getCommune());
            $order->getBillingAddress()->setCommuneId($quote->getShippingAddress()->getCommuneId());
            $order->getBillingAddress()->setCommune($quote->getShippingAddress()->getCommune());
            $order->getBillingAddress()->setRut($quote->getShippingAddress()->getRut());
            $order->getBillingAddress()->setAgencyId($quote->getShippingAddress()->getAgencyId());
        }
        return $this;
    }
}
