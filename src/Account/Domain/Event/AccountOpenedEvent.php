<?php

namespace Kisztof\BankTransferExample\Account\Domain\Event;

use Kisztof\BankTransferExample\Shared\Domain\Event\AbstractDomainEvent;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Money;

final readonly class AccountOpenedEvent extends AbstractDomainEvent
{
    public function __construct(public AccountId $accountId, public Money $initialBalance)
    {
        parent::__construct();
    }

}
