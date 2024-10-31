<?php

namespace Kisztof\BankTransferExample\Account\Domain\Entity;

use Kisztof\BankTransferExample\Account\Domain\Event\AccountOpenedEvent;
use Kisztof\BankTransferExample\Account\Domain\Event\TransferInitiatedEvent;
use Kisztof\BankTransferExample\Account\Domain\Event\TransferReceivedEvent;
use Kisztof\BankTransferExample\Account\Domain\ValueObject\TransferId;
use Kisztof\BankTransferExample\Shared\Domain\Event\DomainEvent;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Money;

final class Account
{
    private AccountId $id;
    private Money $balance;
    private array $recordedEvents = [];

    private function __construct()
    {
    }

    public static function open(AccountId $id, Money $initialBalance): self
    {
        $account = new self();
        $event = new AccountOpenedEvent(
            $id, $initialBalance
        );
        $account->recordEvent($event);

        return $account;
    }

    public static function rehydrate(AccountId $id, array $events): self
    {
        $account = new self();
        foreach ($events as $event) {
            $account->apply($event);
        }

        return $account;
    }

    public function getId(): AccountId
    {
        return $this->id;
    }

    public function getBalance(): Money
    {
        return $this->balance;
    }


    public function initiateTransfer(AccountId $creditorAccountId, Money $amount): TransferId
    {
        $transferId = new TransferId();

        $event = new TransferInitiatedEvent(
            $this->id,
            $creditorAccountId,
            $amount,
            $transferId,
        );

        $this->recordEvent($event);

        return $transferId;
    }

    public function receiveTransfer(AccountId $debtorAccountId, Money $amount, TransferId $transferId): void
    {
        $event = new TransferReceivedEvent(
            $debtorAccountId,
            $this->id,
            $amount,
            $transferId,
        );

        $this->recordEvent($event);
    }

    private function apply(DomainEvent $event): void
    {
        if ($event instanceof AccountOpenedEvent) {
            $this->id = $event->accountId;
            $this->balance = $event->initialBalance;
        } elseif ($event instanceof TransferInitiatedEvent) {
            $this->balance = $this->balance->subtract($event->amount);
        } elseif ($event instanceof TransferReceivedEvent) {
            $this->balance = $this->balance->add($event->amount);
        }
    }

    private function recordEvent(DomainEvent $event): void
    {
        $this->apply($event);
        $this->recordedEvents[] = $event;
    }

    /**
     * @return DomainEvent[]
     */
    public function getRecordedEvents(): array
    {
        return $this->recordedEvents;
    }

    public function clearRecordedEvents(): void
    {
        $this->recordedEvents = [];
    }
}
