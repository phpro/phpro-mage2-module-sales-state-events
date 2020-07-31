<?php
namespace Phpro\SalesStateEvents\Observer\Order;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Phpro\SalesStateEvents\Event\StateDispatcher;

/**
 * Class AfterSaveCommit
 * @package Phpro\SalesStateEvents\Observer\Order
 */
class AfterSaveCommit implements ObserverInterface
{
    /**
     * @var StateDispatcher
     */
    private $dispatcher;

    /**
     * AfterSaveCommit constructor.
     * @param StateDispatcher $dispatcher
     */
    public function __construct(StateDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        $oldState = $order->getOrigData('state');
        $oldStatus = $order->getOrigData('status');

        if ($oldState != $order->getState()) {
            $this->dispatcher->dispatchStateEvent($order, $order->getState());
        }

        if (($oldState != $order->getState()) && !empty($oldState)) {
            $this->dispatcher->dispatchFromToStateEvent($order, $order->getState(), $oldState);
        }

        if ($oldStatus != $order->getStatus()) {
            $this->dispatcher->dispatchStatusEvent($order, $order->getStatus());
        }

        if (($oldStatus != $order->getStatus()) && !empty($oldState)) {
            $this->dispatcher->dispatchFromToStatusEvent($order, $order->getStatus(), $oldStatus);
        }

        if (($oldState != $order->getState()) || ($oldStatus != $order->getStatus())) {
            $this->dispatcher->dispatchStatusInStateEvent($order, $order->getStatus(), $order->getState());
        }
    }
}
