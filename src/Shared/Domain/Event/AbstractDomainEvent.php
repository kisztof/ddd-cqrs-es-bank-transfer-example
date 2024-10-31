<?php

namespace Kisztof\BankTransferExample\Shared\Domain\Event;

use Kisztof\BankTransferExample\Shared\Domain\ValueObject\EventId;

abstract readonly class AbstractDomainEvent implements DomainEvent
{
    private EventId $eventId;
    private \DateTimeImmutable $occurredOn;

    public function __construct()
    {
        $this->eventId = new EventId();
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getEventId(): EventId
    {
        return $this->eventId;
    }

    public function getOccurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
