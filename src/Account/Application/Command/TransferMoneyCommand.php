<?php

namespace Kisztof\BankTransferExample\Account\Application\Command;

use Kisztof\BankTransferExample\Shared\Infrastructure\CommandBus\Command;

final readonly class TransferMoneyCommand implements Command
{

    public function __construct(
        public string $fromAccountId,
        public string $toAccountId,
        public int $amount,
        public string $currencyCode
    ) {
    }
}
