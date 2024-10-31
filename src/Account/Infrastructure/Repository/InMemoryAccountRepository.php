<?php

namespace Kisztof\BankTransferExample\Account\Infrastructure\Repository;

use Kisztof\BankTransferExample\Account\Domain\Entity\Account;
use Kisztof\BankTransferExample\Account\Domain\Repository\AccountRepository;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;
use Kisztof\BankTransferExample\Shared\Infrastructure\EventStore\EventStore;

final readonly class InMemoryAccountRepository implements AccountRepository
{

    public function __construct(private EventStore $eventStore)
    {
    }

    public function get(AccountId $id): Account
    {
        $events = $this->eventStore->loadStream($id);
        if (empty($events)) {
            throw new \RuntimeException('Account not found.');
        }

        return Account::rehydrate($id, $events);
    }

    public function save(Account $account): void
    {
        $events = $account->getRecordedEvents();

        $this->eventStore->appendToStream(
            $account->getId(),
            $events,
        );

        $account->clearRecordedEvents();
    }
}
