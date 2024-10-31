<?php

namespace Kisztof\BankTransferExample\Shared\Domain\Event;

use Kisztof\BankTransferExample\Shared\Domain\ValueObject\EventId;

interface DomainEvent
{
    public function getEventId(): EventId;

    public function getOccurredOn(): \DateTimeImmutable;
}
