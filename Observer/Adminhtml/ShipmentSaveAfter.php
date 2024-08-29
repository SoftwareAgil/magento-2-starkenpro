<?php
/**
 * Admin API validations observer
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Observer\Adminhtml;

use SoftwareAgil\StarkenPro\Helper\Data;
use Magento\Shipping\Model\CarrierFactoryInterface;

/**
 * Shipment Save After to create shipping label.
 */
class ShipmentSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \SoftwareAgil\StarkenPro\Helper\Data
     */
    private $_helper;

    /**
     * @var \Magento\Shipping\Model\CarrierFactoryInterface
     */
    private $_carrierFactory;

    /**
     * ApiValidate constructor.
     *
     * @param \SoftwareAgil\StarkenPro\Helper\Data $helperData
     * @param \Magento\Shipping\Model\CarrierFactoryInterface $carrierFactory
     */
    public function __construct(
        Data $helperData,
        CarrierFactoryInterface $carrierFactory
    ) {
        $this->_helper         = $helperData;
        $this->_carrierFactory = $carrierFactory;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        if ($shipment->getSaSpShippingLabelId()) return $this;
        if ($shipment->getGeneratingShippingLabel()) return $this;
        $order = $shipment->getOrder();
        $shipmentCarrier = $this->_carrierFactory->get($order->getShippingMethod(true)->getCarrierCode());
        if ($shipmentCarrier->getCarrierCode() != "starkenpro") return $this;
        $shipmentCarrier->setGeneratingShippingLabel(true);
        $shipmentCarrier->generateShippingLabelId($shipment);
        $shipmentCarrier->unsetGeneratingShippingLabel();
    }
 }
