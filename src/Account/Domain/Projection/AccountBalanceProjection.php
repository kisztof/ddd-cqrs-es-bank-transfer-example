<?php

namespace Kisztof\BankTransferExample\Account\Domain\Projection;

use Kisztof\BankTransferExample\Shared\Domain\Event\DomainEvent;

interface AccountBalanceProjection
{
    public function project(DomainEvent $event): void;
}
