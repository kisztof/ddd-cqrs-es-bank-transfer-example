<?php

namespace Kisztof\BankTransferExample\Shared\Infrastructure\EventStore;

use Kisztof\BankTransferExample\Shared\Domain\Event\DomainEvent;

final class InMemoryEventStore implements EventStore
{
    /**
     * @var array<string, DomainEvent[]>
     */
    private array $streams = [];

    public function appendToStream(AggregateId $aggregateId, array $events): void
    {
        $id = $aggregateId->getValue();

        foreach ($events as $event) {
            $this->streams[$id][] = $event;
        }
    }

    public function loadStream(AggregateId $aggregateId): array
    {
        $id = $aggregateId->getValue();
        return $this->streams[$id] ?? [];
    }
}
