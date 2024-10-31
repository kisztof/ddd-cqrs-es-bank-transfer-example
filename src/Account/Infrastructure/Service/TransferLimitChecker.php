<?php

namespace Kisztof\BankTransferExample\Account\Infrastructure\Service;

use Kisztof\BankTransferExample\Account\Domain\Projection\TransferProjection;
use Kisztof\BankTransferExample\Account\Domain\Service\TransferLimitCheckerInterface;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;

final readonly class TransferLimitChecker implements TransferLimitCheckerInterface
{
    public function __construct(private TransferProjection $transferProjection)
    {
    }

    public function getTransfersCount(AccountId $accountId, \DateTimeImmutable $date): int
    {
        return $this->transferProjection->getTransfersCount($accountId, $date);
    }
}
