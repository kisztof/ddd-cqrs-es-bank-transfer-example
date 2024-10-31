<?php

namespace Kisztof\BankTransferExample\Account\Domain\Projection;

use Kisztof\BankTransferExample\Shared\Domain\Event\DomainEvent;

interface TransferProjection
{
    public function project(DomainEvent $event): void;
}
