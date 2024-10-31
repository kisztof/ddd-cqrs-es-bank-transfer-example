<?php

namespace Kisztof\BankTransferExample\Account\Domain\Service;

use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;

interface TransferLimitCheckerInterface
{
    public function getTransfersCount(AccountId $accountId, \DateTimeImmutable $date): int;
}
