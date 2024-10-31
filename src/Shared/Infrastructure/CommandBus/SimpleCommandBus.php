<?php

namespace Kisztof\BankTransferExample\Shared\Infrastructure\CommandBus;

final class SimpleCommandBus implements CommandBus
{
    private array $handlers = [];

    public function registerHandler(string $commandClass, CommandHandler $handler): void
    {
        $this->handlers[$commandClass] = $handler;
    }

    public function dispatch(Command $command): void
    {
        $commandClass = get_class($command);
        if (!isset($this->handlers[$commandClass])) {
            throw new \InvalidArgumentException("No handler registered for command {$commandClass}");
        }

        $this->handlers[$commandClass]->handle($command);
    }
}
