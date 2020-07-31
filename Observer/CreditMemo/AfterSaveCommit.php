<?php
namespace Phpro\SalesStateEvents\Observer\CreditMemo;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Phpro\SalesStateEvents\Event\StateDispatcher;

/**
 * Class AfterSaveCommit
 * @package Phpro\SalesStateEvents\Observer\CreditMemo
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
        /** @var Creditmemo $creditMemo */
        $creditMemo = $observer->getEvent()->getCreditmemo();
        $oldState = $creditMemo->getOrigData('state');

        if ($oldState != $creditMemo->getState()) {
            $this->dispatcher->dispatchStateEvent($creditMemo, $creditMemo->getStateName()->getText());
        }

        if (($oldState != $creditMemo->getState()) && !empty($oldState)) {
            $this->dispatcher->dispatchFromToStateEvent(
                $creditMemo,
                $creditMemo->getStateName()->getText(),
                $creditMemo->getStateName($oldState)->getText()
            );
        }
    }
}
