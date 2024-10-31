<?php

namespace Kisztof\BankTransferExample\Account\Domain\Event;

use Kisztof\BankTransferExample\Account\Domain\ValueObject\TransferId;
use Kisztof\BankTransferExample\Shared\Domain\Event\AbstractDomainEvent;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Money;

final readonly class TransferInitiatedEvent extends AbstractDomainEvent
{
    public function __construct(
        public AccountId $debtorAccountId,
        public AccountId $creditorAccountId,
        public Money $amount,
        public TransferId $transferId
    ) {
        parent::__construct();
    }

}
