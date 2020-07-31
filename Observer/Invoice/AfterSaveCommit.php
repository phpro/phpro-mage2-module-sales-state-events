<?php
namespace Phpro\SalesStateEvents\Observer\Invoice;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Invoice;
use Phpro\SalesStateEvents\Event\StateDispatcher;

/**
 * Class AfterCommittedSave
 * @package Mediahuis\Sales\Observer\Invoice
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
        /** @var Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $oldState = $invoice->getOrigData('state');

        if ($oldState != $invoice->getState()) {
            $this->dispatcher->dispatchStateEvent($invoice, $invoice->getStateName()->getText());
        }

        if (($oldState != $invoice->getState()) && !empty($oldState)) {
            $this->dispatcher->dispatchFromToStateEvent(
                $invoice,
                $invoice->getStateName()->getText(),
                $invoice->getStateName($oldState)->getText()
            );
        }
    }
}
