<?php

namespace Kisztof\BankTransferExample\Shared\Infrastructure\EventBus;

use Kisztof\BankTransferExample\Shared\Domain\Event\DomainEvent;

interface EventBus
{
    public function publish(DomainEvent $event): void;

    public function subscribe(string $eventClass, callable $handler): void;
}
