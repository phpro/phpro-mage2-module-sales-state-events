<?php
namespace Phpro\StateEvents\Test\Unit\Order;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Phpro\SalesStateEvents\Event\StateDispatcher;
use Phpro\SalesStateEvents\Observer\Order\AfterSaveCommit;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AfterSaveCommitTest
 * @package Phpro\StateEvents\Test\Unit\Order
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
     * @var MockObject|Order
     */
    private $order;

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

        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testItWillTriggerStateAndStatusEvents()
    {
        $state = 'compete';
        $status = 'shipped';
        $oldState = 'new';
        $oldStatus = 'pending';

        $this->prepareOrder($state, $status, $oldState, $oldStatus);
        $this->event->setOrder($this->order);

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchStateEvent')
            ->with($this->order, $state);

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchFromToStateEvent')
            ->with($this->order, $state, $oldState);

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchStatusEvent')
            ->with($this->order, $status);

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchFromToStatusEvent')
            ->with($this->order, $status, $oldStatus);

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchStatusInStateEvent')
            ->with($this->order, $status, $state);

        $listener = new AfterSaveCommit($this->stateDispatcher);
        $listener->execute($this->observer);
    }

    public function testItWillOnlyTriggerStateAndStatusEventsOnChange()
    {
        $state = 'compete';
        $status = 'shipped';

        $this->prepareOrder($state, $status, $state, $status);
        $this->event->setOrder($this->order);

        $this->stateDispatcher->expects($this->never())
            ->method('dispatchStateEvent');

        $this->stateDispatcher->expects($this->never())
            ->method('dispatchFromToStateEvent');

        $this->stateDispatcher->expects($this->never())
            ->method('dispatchStatusEvent');

        $this->stateDispatcher->expects($this->never())
            ->method('dispatchFromToStatusEvent');

        $this->stateDispatcher->expects($this->never())
            ->method('dispatchStatusInStateEvent');

        $listener = new AfterSaveCommit($this->stateDispatcher);
        $listener->execute($this->observer);
    }

    public function testItWillNotTriggerFromToEventForNewObject()
    {
        $state = 'new';
        $status = 'pending';

        $this->prepareOrder($state, $status);
        $this->event->setOrder($this->order);

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchStateEvent');

        $this->stateDispatcher->expects($this->never())
            ->method('dispatchFromToStateEvent');

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchStatusEvent');

        $this->stateDispatcher->expects($this->never())
            ->method('dispatchFromToStatusEvent');

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchStatusInStateEvent');

        $listener = new AfterSaveCommit($this->stateDispatcher);
        $listener->execute($this->observer);
    }

    /**
     * @param string $state
     * @param string $status
     * @param string|null $oldState
     * @param string|null $oldStatus
     */
    private function prepareOrder($state, $status, $oldState = null, $oldStatus = null)
    {
        $this->order->expects($this->exactly(2))
            ->method('getOrigData')
            ->withConsecutive(['state'], ['status'])
            ->willReturnOnConsecutiveCalls($oldState, $oldStatus);

        $this->order->expects($this->atLeastOnce())
            ->method('getState')
            ->willReturn($state);

        $this->order->expects($this->atLeastOnce())
            ->method('getStatus')
            ->willReturn($status);
    }
}
