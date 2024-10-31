<?php

namespace Kisztof\BankTransferExample\Shared\Infrastructure\EventStore;

use Kisztof\BankTransferExample\Shared\Domain\Event\DomainEvent;

interface EventStore
{
    /**
     * @param DomainEvent[] $events
     */
    public function appendToStream(AggregateId $aggregateId, array $events): void;

    /**
     * @return DomainEvent[]
     */
    public function loadStream(AggregateId $aggregateId): array;
}
