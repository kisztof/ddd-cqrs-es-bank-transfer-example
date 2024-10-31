<?php

namespace Kisztof\BankTransferExample\Account\Infrastructure\Projection;

use Kisztof\BankTransferExample\Account\Domain\Event\TransferInitiatedEvent;
use Kisztof\BankTransferExample\Account\Domain\Projection\TransferProjection as TransferProjectionInterface;
use Kisztof\BankTransferExample\Shared\Domain\Event\DomainEvent;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;

final class TransferProjection implements TransferProjectionInterface
{
    /**
     * @var array<string, array<string, int>>
     */
    private array $transfers = [];

    public function project(DomainEvent $event): void
    {
        if ($event instanceof TransferInitiatedEvent) {
            $this->projectTransferInitiated($event);
        }
    }

    private function projectTransferInitiated(TransferInitiatedEvent $event): void
    {
        $dateKey = $event->getOccurredOn()->format('Y-m-d');
        $accountId = $event->debtorAccountId->getValue();

        if (!isset($this->transfers[$accountId][$dateKey])) {
            $this->transfers[$accountId][$dateKey] = 0;
        }

        $this->transfers[$accountId][$dateKey]++;
    }

    public function getTransfersCount(AccountId $accountId, \DateTimeImmutable $date): int
    {
        $dateKey = $date->format('Y-m-d');
        $accountKey = $accountId->getValue();

        return $this->transfers[$accountKey][$dateKey] ?? 0;
    }
}
