<?php

namespace Kisztof\BankTransferExample\Account\Application\Command;

use Kisztof\BankTransferExample\Account\Domain\Service\TransferRuleChecker;
use Kisztof\BankTransferExample\Account\Domain\Service\TransferService;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Currency;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Money;
use Kisztof\BankTransferExample\Shared\Infrastructure\EventBus\EventBus;

final readonly class TransferMoneyCommandHandler
{
    public function __construct(
        private TransferService $transferService,
        private TransferRuleChecker $ruleChecker,
        private EventBus $eventBus
    ) {
    }

    public function handle(TransferMoneyCommand $command): void
    {
        $fromAccountId = new AccountId($command->fromAccountId);
        $fromAccount = $this->transferService->getAccount($fromAccountId);

        $amount = new Money($command->amount, new Currency($command->currencyCode));
        $fee = $this->ruleChecker->check($fromAccount, $amount);

        $recordedEvents = $this->transferService->transfer(
            $fromAccountId,
            new AccountId($command->toAccountId),
            $amount,
            $fee
        );

        foreach ($recordedEvents as $event) {
            $this->eventBus->publish($event);
        }
    }
}
