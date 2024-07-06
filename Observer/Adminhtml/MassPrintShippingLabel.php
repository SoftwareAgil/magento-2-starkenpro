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
use Magento\Shipping\Model\CarrierFactory;
use Magento\Sales\Model\OrderRepository;

/**
 * Shipment Save After to create shipping label.
 */
class MassPrintShippingLabel implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \SoftwareAgil\StarkenPro\Helper\Data
     */
    private $_helper;

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    private $_carrierFactory;

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    private $_orderRepository;

    /**
     * ApiValidate constructor.
     *
     * @param \SoftwareAgil\StarkenPro\Helper\Data $helperData
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     */
    public function __construct(
        Data $helperData,
        CarrierFactory $carrierFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository
    ) {
        $this->_helper         = $helperData;
        $this->_carrierFactory = $carrierFactory;
        $this->_orderRepository = $orderRepository;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $selectedOrders = (array)$request->getParam('selected');
        foreach ($selectedOrders as $oi) {
            $order = $this->_orderRepository->get($oi);
            $shipmentCarrier = $this->_carrierFactory->create($order->getShippingMethod(true)->getCarrierCode());
            if ($shipmentCarrier->getCarrierCode() != "starkenpro") continue;
            $shipmentCarrier->generateShippingLabel($order);
        }
    }
 }
