![](https://github.com/phpro/phpro-mage2-module-sales-state-events/workflows/.github/workflows/grumphp.yml/badge.svg)

# Sales State Events for Magento 2

This Magento module will add new events for the Magento Sales module. The events are based on state and status changes on orders, invoices and credit memos.

This makes it easier to trigger custom functionality based on state changes.

## Installation

    composer require phpro/mage2-module-sales-state-events

## Key features

### Order
Events will be dispatched after an order is successfully saved with a new state or with a new status. It will create events for all statuses, standard or configured.

An event with the new state/status name will be dispatched, for example:

- sales_order_state_new
- sales_order_state_complete
- sales_order_status_pending_payment
- sales_order_status_new

An event with the new state/status name and the former state/status name will be dispatched, for example:

- sales_order_state_complete_to_closed
- sales_order_state_new_to_canceled
- sales_order_status_new_to_canceled
- sales_order_status_processing_to_fraud

An event with the (new) state name and the (new) status will be dispatched, for example:

- sales_order_state_new_status_authorized
- sales_order_state_processing_status_fraud

### Invoice

Events will be dispatched after an invoice is successfully saved with a new state.

An event with the new state name will be dispatched, for example:

- sales_order_invoice_state_canceled
- sales_order_invoice_state_paid

An event with the new state name and the former state name will be dispatched, for example:

- sales_order_invoice_state_pending_to_paid
- sales_order_invoice_state_pending_to_canceled

### Credit Memo

Events will be dispatched after a credit memo is successfully saved with a new state.

An event with the new state name will be dispatched, for example:

- sales_order_creditmemo_state_canceled
- sales_order_creditmemo_state_refunded

An event with the new state name and the former state name will be dispatched, for example:

- sales_order_creditmemo_state_pending_to_refunded
- sales_order_creditmemo_state_pending_to_canceled
