<?php
namespace Phpro\StateEvents\Test\Unit\Event;

use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Phpro\SalesStateEvents\Event\StateDispatcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class StateDispatcherTest
 * @package Phpro\StateEvents\Test\Unit\Event
 */
class StateDispatcherTest extends TestCase
{
    /**
     * @var MockObject|ManagerInterface
     */
    private $dispatcherMock;

    /**
     * @var StateDispatcher
     */
    private $stateDispatcher;

    /**
     * @var MockObject|Invoice
     */
    private $invoiceMock;

    /**
     * @var MockObject|Order
     */
    private $orderMock;

    /**
     * @var MockObject|LoggerInterface
     */
    private $logger;

    /**
     * Is called before running a test
     */
    protected function setUp()
    {
        $this->dispatcherMock = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->stateDispatcher = new StateDispatcher($this->dispatcherMock, $this->logger);

        $this->invoiceMock = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testItDispatchesAStateEvent()
    {
        $eventName = 'state_pending_generation';

        $this->prepareTestDispatchEvent($this->invoiceMock, $eventName);
        $this->stateDispatcher->dispatchStateEvent($this->invoiceMock, 'Pending generation');
    }

    public function testItDispatchesAStatusEvent()
    {
        $eventName = 'status_pending_payment';

        $this->prepareTestDispatchEvent($this->orderMock, $eventName);
        $this->stateDispatcher->dispatchStatusEvent($this->orderMock, 'Pending payment');
    }

    public function testItDispatchesAStatusInStateEvent()
    {
        $eventName = 'state_pending_status_pending_payment';

        $this->prepareTestDispatchEvent($this->orderMock, $eventName);
        $this->stateDispatcher->dispatchStatusInStateEvent($this->orderMock, 'Pending payment', 'Pending');
    }

    public function testItDispatchesAFromToStateEvent()
    {
        $eventName = 'state_pending_to_paid';

        $this->prepareTestDispatchEvent($this->invoiceMock, $eventName);
        $this->stateDispatcher->dispatchFromToStateEvent($this->invoiceMock, 'Paid', 'Pending');
    }

    public function testItDispatchesAFromToStatusEvent()
    {
        $eventName = 'status_new_to_canceled';

        $this->prepareTestDispatchEvent($this->orderMock, $eventName);
        $this->stateDispatcher->dispatchFromToStatusEvent($this->orderMock, 'Canceled', 'New');
    }

    /**
     * @param MockObject $salesModelMock
     * @param string $eventName
     * @param string $eventPrefix
     * @param string $eventObject
     */
    private function prepareTestDispatchEvent(
        MockObject $salesModelMock,
        $eventName,
        $eventPrefix = 'prefix',
        $eventObject = 'object'
    ) {
        $salesModelMock->expects($this->once())
            ->method('getEventPrefix')
            ->willReturn($eventPrefix);

        $salesModelMock->expects($this->once())
            ->method('getEventObject')
            ->willReturn($eventObject);

        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with($eventPrefix . '_' . $eventName, [$eventObject => $salesModelMock]);
    }
}
