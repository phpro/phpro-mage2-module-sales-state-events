<?php
namespace Phpro\StateEvents\Test\Unit\Invoice;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order\Invoice;
use Phpro\SalesStateEvents\Event\StateDispatcher;
use Phpro\SalesStateEvents\Observer\Invoice\AfterSaveCommit;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AfterSaveCommitTest
 * @package Phpro\StateEvents\Test\Unit\Invoice
 */
class AfterSaveCommitTest extends TestCase
{
    /**
     * @var MockObject|StateDispatcher
     */
    private $stateDispatcher;

    /**
     * @var Observer
     */
    private $observer;

    /**
     * @var Event
     */
    private $event;

    /**
     * @var MockObject|Invoice
     */
    private $invoice;

    /**
     * Is called before running a test
     */
    protected function setUp()
    {
        $this->stateDispatcher = $this->getMockBuilder(StateDispatcher::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = new Observer();
        $this->event = new Event();

        $this->observer->setEvent($this->event);

        $this->invoice = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testItWillTriggerStateEvents()
    {
        $state = 'canceled';
        $oldState = 'pending';

        $this->prepareInvoice($state, $oldState);
        $this->event->setInvoice($this->invoice);

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchStateEvent')
            ->with($this->invoice, $state);

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchFromToStateEvent')
            ->with($this->invoice, $state, $oldState);

        $listener = new AfterSaveCommit($this->stateDispatcher);
        $listener->execute($this->observer);
    }

    public function testItWillOnlyTriggerStateEventsOnChange()
    {
        $state = 'canceled';

        $this->prepareInvoice($state, $state);
        $this->event->setInvoice($this->invoice);

        $this->stateDispatcher->expects($this->never())
            ->method('dispatchStateEvent');

        $this->stateDispatcher->expects($this->never())
            ->method('dispatchFromToStateEvent');

        $listener = new AfterSaveCommit($this->stateDispatcher);
        $listener->execute($this->observer);
    }

    public function testItWillNotTriggerFromToEventForNewObject()
    {
        $state = 'canceled';

        $this->prepareInvoice($state);
        $this->event->setInvoice($this->invoice);

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchStateEvent');

        $this->stateDispatcher->expects($this->never())
            ->method('dispatchFromToStateEvent');

        $listener = new AfterSaveCommit($this->stateDispatcher);
        $listener->execute($this->observer);
    }

    /**
     * @param string $state
     * @param string|null $oldState
     */
    private function prepareInvoice($state, $oldState = null)
    {
        $this->invoice->expects($this->once())
            ->method('getOrigData')
            ->with('state')
            ->willReturn($oldState);

        $this->invoice->expects($this->exactly(2))
            ->method('getState')
            ->willReturn($state);

        $this->invoice->expects($this->any())
            ->method('getStateName')
            ->withConsecutive([null], [null], [$oldState])
            ->willReturnOnConsecutiveCalls(new Phrase($state), new Phrase($state), new Phrase($oldState));
    }
}
