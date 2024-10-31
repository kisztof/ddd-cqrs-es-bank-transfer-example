<?php

namespace Kisztof\BankTransferExample\Account\Domain\Service;

use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Money;

final class PercentageFeeCalculator implements FeeCalculatorInterface
{
    private float $feePercentage;

    public function __construct(float $feePercentage)
    {
        $this->feePercentage = $feePercentage;
    }

    public function calculateFee(Money $amount): Money
    {
        $feeAmount = (int) round($amount->amount * ($this->feePercentage / 100));
        return new Money($feeAmount, $amount->currency);
    }
}
