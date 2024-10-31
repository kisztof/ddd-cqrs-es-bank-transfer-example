<?php

namespace Kisztof\BankTransferExample\Account\Infrastructure\Projection;

use Kisztof\BankTransferExample\Account\Domain\Event\AccountOpenedEvent;
use Kisztof\BankTransferExample\Account\Domain\Event\TransferInitiatedEvent;
use Kisztof\BankTransferExample\Account\Domain\Event\TransferReceivedEvent;
use Kisztof\BankTransferExample\Account\Domain\Projection\AccountBalanceProjection as AccountBalanceProjectionInterface;
use Kisztof\BankTransferExample\Shared\Domain\Event\DomainEvent;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Currency;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Money;

final class AccountBalanceProjection implements AccountBalanceProjectionInterface
{
    /**
     * @var array<string, Money>
     */
    private array $balances = [];

    public function project(DomainEvent $event): void
    {
        if ($event instanceof AccountOpenedEvent) {
            $this->projectAccountOpen($event);
        } elseif ($event instanceof TransferInitiatedEvent) {
            $this->projectTransferInitiated($event);
        } elseif ($event instanceof TransferReceivedEvent) {
            $this->projectTransferReceived($event);
        }
    }

    private function projectAccountOpen(AccountOpenedEvent $event): void
    {
        $accountId = $event->accountId->getValue();
        $initialBalance = $event->initialBalance;

        $this->balances[$accountId] = $initialBalance;
    }

    private function projectTransferInitiated(TransferInitiatedEvent $event): void
    {
        $fromAccountId = $event->debtorAccountId->getValue();
        $amount = $event->amount;

        $this->debitBalance($fromAccountId, $amount);
    }

    private function projectTransferReceived(TransferReceivedEvent $event): void
    {
        $toAccountId = $event->creditorAccountId->getValue();
        $amount = $event->amount;

        $this->creditBalance($toAccountId, $amount);
    }

    private function creditBalance(string $accountId, Money $amount): void
    {
        if (!isset($this->balances[$accountId])) {
            $this->balances[$accountId] = new Money(0, $amount->currency);
        }

        $this->balances[$accountId] = $this->balances[$accountId]->add($amount);
    }

    private function debitBalance(string $accountId, Money $amount): void
    {
        if (!isset($this->balances[$accountId])) {
            $this->balances[$accountId] = new Money(0, $amount->currency);
        }

        $this->balances[$accountId] = $this->balances[$accountId]->subtract($amount);
    }

    public function getBalance(AccountId $accountId): Money
    {
        $accountKey = $accountId->getValue();
        return $this->balances[$accountKey] ?? new Money(0, new Currency('USD'));
    }
}

