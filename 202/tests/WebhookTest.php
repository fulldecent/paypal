<?php

namespace PaypalTests;

use Order;
use PaypalAddons\classes\Webhook\WebhookEventHandler;
use PaypalAddons\services\StatusMapping;

class WebhookTest extends AbstractTest
{
    public function testCompleted()
    {
        $event = $this->initWebhookEvent('event-completed.json');
        $statusMap = new StatusMapping();
        $webhookHandler = new WebhookEventHandler();
        $idOrder = $this->createOrderForWebhookEvent($event, $statusMap->getWaitValidationStatus());
        $webhookHandler->handle($event);
        $order = new Order($idOrder);

        $this->assertEquals($statusMap->getAcceptedStatus(), $order->current_state);
    }

    public function testPendingEvent()
    {
        $event = $this->initWebhookEvent('event-pending.json');
        $statusMap = new StatusMapping();
        $webhookHandler = new WebhookEventHandler();
        $idOrder = $this->createOrderForWebhookEvent($event, $statusMap->getWaitValidationStatus());
        $webhookHandler->handle($event);
        $order = new Order($idOrder);

        $this->assertEquals($statusMap->getWaitValidationStatus(), $order->current_state);
    }
}
