<?php

namespace Kisztof\BankTransferExample\Account\Application\Command;

use Kisztof\BankTransferExample\Shared\Infrastructure\CommandBus\Command;

final readonly class OpenAccountCommand implements Command
{

    public function __construct(public string $currencyCode)
    {
    }
}
