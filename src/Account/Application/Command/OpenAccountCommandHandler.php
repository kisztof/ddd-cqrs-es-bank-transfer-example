<?php

namespace Kisztof\BankTransferExample\Account\Application\Command;


use Kisztof\BankTransferExample\Account\Domain\Entity\Account;
use Kisztof\BankTransferExample\Account\Domain\Repository\AccountRepository;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Currency;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Money;
use Kisztof\BankTransferExample\Shared\Infrastructure\CommandBus\Command;
use Kisztof\BankTransferExample\Shared\Infrastructure\CommandBus\CommandHandler;
use Kisztof\BankTransferExample\Shared\Infrastructure\EventBus\EventBus;

final class OpenAccountCommandHandler implements CommandHandler
{
    public function __construct(private AccountRepository $repository, private EventBus $eventBus)
    {
    }

    public function handle(Command $command): void
    {
        if (!$command instanceof OpenAccountCommand) {
            throw new \InvalidArgumentException('Invalid command type');
        }

        $account = Account::open(
            new AccountId(),
            new Money(0, new Currency($command->currencyCode))
        );

        $recordedEvents = $account->getRecordedEvents();

        $this->repository->save($account);

        foreach ($recordedEvents as $event) {
            $this->eventBus->publish($event);
        }
    }
}
