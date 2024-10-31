<?php

namespace Kisztof\BankTransferExample\Shared\Infrastructure\CommandBus;

interface CommandHandler
{
    public function handle(Command $command): void;
}
