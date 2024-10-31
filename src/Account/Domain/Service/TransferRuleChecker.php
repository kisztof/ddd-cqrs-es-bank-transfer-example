<?php

namespace Kisztof\BankTransferExample\Account\Domain\Service;

use Kisztof\BankTransferExample\Account\Domain\Entity\Account;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Money;

final readonly class TransferRuleChecker
{
    public function __construct(
        private TransferLimitCheckerInterface $transferLimitChecker,
        private int $dailyLimit,
        private FeeCalculatorInterface $feeCalculator,
    ) {
    }

    public function check(Account $fromAccount, Money $amount): Money
    {
        $today = new \DateTimeImmutable('today');
        $transfersToday = $this->transferLimitChecker->getTransfersCount($fromAccount->getId(), $today);

        if ($transfersToday >= $this->dailyLimit) {
            throw new \DomainException('Daily transfer limit reached.');
        }

        $fee = $this->feeCalculator->calculateFee($amount);

        if (!$amount->currency->equals($fee->currency)) {
            throw new \InvalidArgumentException('Currency mismatch between amount and fee.');
        }

        $totalAmount = $amount->add($fee);

        if ($fromAccount->getBalance()->amount < $totalAmount->amount) {
            throw new \DomainException('Insufficient funds including transfer fee.');
        }

        return $fee;
    }
}
