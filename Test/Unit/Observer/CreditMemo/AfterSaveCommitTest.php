<?php
namespace Phpro\StateEvents\Test\Unit\CreditMemo;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order\Creditmemo;
use Phpro\SalesStateEvents\Event\StateDispatcher;
use Phpro\SalesStateEvents\Observer\CreditMemo\AfterSaveCommit;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AfterSaveCommitTest
 * @package Phpro\StateEvents\Test\Unit\CreditMemo
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
     * @var MockObject|Creditmemo
     */
    private $creditMemo;

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

        $this->creditMemo = $this->getMockBuilder(Creditmemo::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testItWillTriggerStateEvents()
    {
        $state = 'canceled';
        $oldState = 'pending';

        $this->prepareCreditMemo($state, $oldState);
        $this->event->setCreditmemo($this->creditMemo);

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchStateEvent')
            ->with($this->creditMemo, $state);

        $this->stateDispatcher->expects($this->once())
            ->method('dispatchFromToStateEvent')
            ->with($this->creditMemo, $state, $oldState);

        $listener = new AfterSaveCommit($this->stateDispatcher);
        $listener->execute($this->observer);
    }

    public function testItWillOnlyTriggerStateEventsOnChange()
    {
        $state = 'canceled';

        $this->prepareCreditMemo($state, $state);
        $this->event->setCreditmemo($this->creditMemo);

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

        $this->prepareCreditMemo($state);
        $this->event->setCreditmemo($this->creditMemo);

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
    private function prepareCreditMemo($state, $oldState = null)
    {
        $this->creditMemo->expects($this->once())
            ->method('getOrigData')
            ->with('state')
            ->willReturn($oldState);

        $this->creditMemo->expects($this->exactly(2))
            ->method('getState')
            ->willReturn($state);

        $this->creditMemo->expects($this->any())
            ->method('getStateName')
            ->withConsecutive([null], [null], [$oldState])
            ->willReturnOnConsecutiveCalls(new Phrase($state), new Phrase($state), new Phrase($oldState));
    }
}
