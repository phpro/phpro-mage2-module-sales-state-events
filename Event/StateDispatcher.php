<?php
namespace Phpro\SalesStateEvents\Event;

use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Model\AbstractModel;
use Psr\Log\LoggerInterface;

/**
 * Class StateDispatcher
 * @package Phpro\SalesStateEvents\Event
 */
class StateDispatcher
{
    /**
     * @var ManagerInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * StateDispatcher constructor.
     * @param ManagerInterface $dispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(ManagerInterface $dispatcher, LoggerInterface $logger)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * @param AbstractModel $salesItem
     * @param string $stateName
     */
    public function dispatchStateEvent(AbstractModel $salesItem, string $stateName)
    {
        $eventName = $salesItem->getEventPrefix();
        $eventName .= '_state_';
        $eventName .= $this->getFormattedString($stateName);

        $this->dispatcher->dispatch($eventName, [$salesItem->getEventObject() => $salesItem]);
        $this->logger->debug('Dispatched event ' . $eventName);
    }

    /**
     * @param AbstractModel $salesItem
     * @param string $statusName
     */
    public function dispatchStatusEvent(AbstractModel $salesItem, string $statusName)
    {
        $eventName = $salesItem->getEventPrefix();
        $eventName .= '_status_';
        $eventName .= $this->getFormattedString($statusName);

        $this->dispatcher->dispatch($eventName, [$salesItem->getEventObject() => $salesItem]);
        $this->logger->debug('Dispatched event ' . $eventName);
    }

    /**
     * @param AbstractModel $salesItem
     * @param string $statusName
     * @param string|null $stateName
     */
    public function dispatchStatusInStateEvent(AbstractModel $salesItem, string $statusName, ?string $stateName)
    {
        $stateName = ($stateName ?: '');
        $eventName = $salesItem->getEventPrefix();
        $eventName .= '_state_';
        $eventName .= $this->getFormattedString($stateName);
        $eventName .= '_status_';
        $eventName .= $this->getFormattedString($statusName);

        $this->dispatcher->dispatch($eventName, [$salesItem->getEventObject() => $salesItem]);
        $this->logger->debug('Dispatched event ' . $eventName);
    }

    /**
     * @param AbstractModel $salesItem
     * @param string $stateName
     * @param string $oldStateName
     */
    public function dispatchFromToStateEvent(AbstractModel $salesItem, string $stateName, string $oldStateName)
    {
        $eventName = $salesItem->getEventPrefix();
        $eventName .= '_state_';
        $eventName .= $this->getFormattedString($oldStateName);
        $eventName .= '_to_';
        $eventName .= $this->getFormattedString($stateName);

        $this->dispatcher->dispatch($eventName, [$salesItem->getEventObject() => $salesItem]);
        $this->logger->debug('Dispatched event ' . $eventName);
    }

    /**
     * @param AbstractModel $salesItem
     * @param string $statusName
     * @param string $oldStatusName
     */
    public function dispatchFromToStatusEvent(AbstractModel $salesItem, string $statusName, string $oldStatusName)
    {
        $eventName = $salesItem->getEventPrefix();
        $eventName .= '_status_';
        $eventName .= $this->getFormattedString($oldStatusName);
        $eventName .= '_to_';
        $eventName .= $this->getFormattedString($statusName);

        $this->dispatcher->dispatch($eventName, [$salesItem->getEventObject() => $salesItem]);
        $this->logger->debug('Dispatched event ' . $eventName);
    }

    /**
     * @param string $value
     * @return string
     */
    private function getFormattedString(string $value)
    {
        return strtolower(str_replace(' ', '_', $value));
    }
}
