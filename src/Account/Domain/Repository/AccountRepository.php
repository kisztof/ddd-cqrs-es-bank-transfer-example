<?php

namespace Kisztof\BankTransferExample\Account\Domain\Repository;

use Kisztof\BankTransferExample\Account\Domain\Entity\Account;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;

interface AccountRepository
{
    public function save(Account $account): void;
    public function get(AccountId $id): Account;
}
