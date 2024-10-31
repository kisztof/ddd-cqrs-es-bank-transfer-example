<?php

namespace Kisztof\BankTransferExample\Account\Domain\Service;

use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Money;

interface FeeCalculatorInterface
{
    public function calculateFee(Money $amount): Money;
}
