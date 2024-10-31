<?php

namespace Kisztof\BankTransferExample\Account\Domain\Service;

use Kisztof\BankTransferExample\Account\Domain\Entity\Account;
use Kisztof\BankTransferExample\Account\Domain\Repository\AccountRepository;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Money;

final readonly class TransferService
{

    public function __construct(private AccountRepository $accountRepository, private AccountId $feeAccountId)
    {
    }

    public function transfer(AccountId $debtorAccountId, AccountId $creditorAccountId, Money $amount, Money $fee): array
    {
        $fromAccount = $this->accountRepository->get($debtorAccountId);
        $toAccount = $this->accountRepository->get($creditorAccountId);
        $feeAccount = $this->accountRepository->get($this->feeAccountId);
        $totalAmount = $amount->add($fee);
        $transferId = $fromAccount->initiateTransfer($creditorAccountId, $totalAmount);
        $toAccount->receiveTransfer($debtorAccountId, $amount, $transferId);
        $feeAccount->receiveTransfer($debtorAccountId, $fee, $transferId);

        $recordedEvents = array_merge(
            $fromAccount->getRecordedEvents(),
            $toAccount->getRecordedEvents(),
            $feeAccount->getRecordedEvents()
        );

        $this->accountRepository->save($fromAccount);
        $this->accountRepository->save($toAccount);
        $this->accountRepository->save($feeAccount);

        return $recordedEvents;
    }

    public function getAccount(AccountId $accountId): Account
    {
        return $this->accountRepository->get($accountId);
    }
}
