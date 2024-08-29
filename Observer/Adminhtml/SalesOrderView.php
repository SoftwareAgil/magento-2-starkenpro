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
use Magento\Sales\Model\OrderRepository;

/**
 * Shipment Save After to create shipping label.
 */
class SalesOrderView implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \SoftwareAgil\StarkenPro\Helper\Data
     */
    private $_helper;

    /**
     * @var CarrierFactoryInterface
     */
    private $_carrierFactory;

    /**
     * @var OrderRepository
     */
    private $_orderRepository;

    /**
     * ApiValidate constructor.
     *
     * @param \SoftwareAgil\StarkenPro\Helper\Data $helperData
     * @param \Magento\Shipping\Model\CarrierFactoryInterface $carrierFactory
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     */
    public function __construct(
        Data $helperData,
        CarrierFactoryInterface $carrierFactory,
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
        $orderId = $request->getParam('order_id');
        if ($orderId) {
            $order = $this->_orderRepository->get($orderId);
            $shipmentCarrier = $this->_carrierFactory->get($order->getShippingMethod(true)->getCarrierCode());
            if ($shipmentCarrier->getCarrierCode() != "starkenpro") return $this;
            $shipmentCarrier->updateShipTrackingInfo($order);
        }
    }
 }
