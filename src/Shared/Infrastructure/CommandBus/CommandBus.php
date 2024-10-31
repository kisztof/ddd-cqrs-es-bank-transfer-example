<?php

namespace Kisztof\BankTransferExample\Shared\Infrastructure\CommandBus;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
