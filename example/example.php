<?php
require_once '../vendor/autoload.php';

use Kisztof\BankTransferExample\Account\Application\Command\TransferMoneyCommand;
use Kisztof\BankTransferExample\Account\Application\Command\TransferMoneyCommandHandler;
use Kisztof\BankTransferExample\Account\Domain\Entity\Account;
use Kisztof\BankTransferExample\Account\Domain\Event\AccountOpenedEvent;
use Kisztof\BankTransferExample\Account\Domain\Event\TransferInitiatedEvent;
use Kisztof\BankTransferExample\Account\Domain\Event\TransferReceivedEvent;
use Kisztof\BankTransferExample\Account\Domain\Service\PercentageFeeCalculator;
use Kisztof\BankTransferExample\Account\Domain\Service\TransferRuleChecker;
use Kisztof\BankTransferExample\Account\Domain\Service\TransferService;
use Kisztof\BankTransferExample\Account\Infrastructure\Projection\AccountBalanceProjection;
use Kisztof\BankTransferExample\Account\Infrastructure\Projection\TransferProjection;
use Kisztof\BankTransferExample\Account\Infrastructure\Repository\InMemoryAccountRepository;
use Kisztof\BankTransferExample\Account\Infrastructure\Service\TransferLimitChecker;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\AccountId;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Currency;
use Kisztof\BankTransferExample\Shared\Domain\ValueObject\Money;
use Kisztof\BankTransferExample\Shared\Infrastructure\EventBus\SimpleEventBus;
use Kisztof\BankTransferExample\Shared\Infrastructure\EventStore\InMemoryEventStore;

// Initialize the event store and repositories
$eventStore = new InMemoryEventStore();
$accountRepository = new InMemoryAccountRepository($eventStore);

// Initialize event bus
$eventBus = new SimpleEventBus();

// Initialize projections
$transferProjection = new TransferProjection();
$accountBalanceProjection = new AccountBalanceProjection();

// Subscribe projections to specific events
$eventBus->subscribe(AccountOpenedEvent::class, function (AccountOpenedEvent $event) use ($accountBalanceProjection) {
    $accountBalanceProjection->project($event);
});

$eventBus->subscribe(TransferInitiatedEvent::class, function (TransferInitiatedEvent $event) use ($transferProjection, $accountBalanceProjection) {
    $transferProjection->project($event);
    $accountBalanceProjection->project($event);
});

$eventBus->subscribe(TransferReceivedEvent::class, function (TransferReceivedEvent $event) use ($accountBalanceProjection) {
    $accountBalanceProjection->project($event);
});


$currencyUSD = new Currency('USD');

$accountAId = new AccountId('accountA');
$accountBId = new AccountId('accountB');
$feeAccountId = new AccountId('feeAccount');

$accountA = Account::open($accountAId, new Money(100000, $currencyUSD));
$accountB = Account::open($accountBId, new Money(0, $currencyUSD));
$feeAccount = Account::open($feeAccountId, new Money(0, $currencyUSD));

// Save accounts (which will save events)
$accountRepository->save($accountA);
$accountRepository->save($accountB);
$accountRepository->save($feeAccount);

// Publish initial events to update projections
$initialEvents = array_merge(
    $eventStore->loadStream($accountAId),
    $eventStore->loadStream($accountBId),
    $eventStore->loadStream($feeAccountId)
);

foreach ($initialEvents as $event) {
    $eventBus->publish($event);
}

// Create transfer limit checker using the projection
$transferLimitChecker = new TransferLimitChecker($transferProjection);

// Create fee calculator with 0.5% fee
$feeCalculator = new PercentageFeeCalculator(0.5);

// Create rule checker with a daily limit of 3 transfers
$ruleChecker = new TransferRuleChecker(
    transferLimitChecker: $transferLimitChecker,
    dailyLimit: 3,
    feeCalculator: $feeCalculator
);

// Create transfer service
$transferService = new TransferService($accountRepository, $feeAccountId);

// Create command handler
$transferMoneyCommandHandler = new TransferMoneyCommandHandler(
    transferService: $transferService,
    ruleChecker: $ruleChecker,
    eventBus: $eventBus
);

// Perform transfers
try {
    for ($i = 1; $i <= 4; $i++) {
        $command = new TransferMoneyCommand(
            fromAccountId: $accountAId->getValue(),
            toAccountId: $accountBId->getValue(),
            amount: 100,
            currencyCode: 'USD'
        );
        $transferMoneyCommandHandler->handle($command);
        echo "Transfer $i completed successfully.\n";
    }
} catch (\Exception $e) {
    echo "Transfer failed: " . $e->getMessage() . "\n";
}

// Output account balances from the read model
$accountABalance = $accountBalanceProjection->getBalance($accountAId)->amount;
$accountBBalance = $accountBalanceProjection->getBalance($accountBId)->amount;
$feeAccountBalance = $accountBalanceProjection->getBalance($feeAccountId)->amount;

echo "Account A Balance: $accountABalance\n";
echo "Account B Balance: $accountBBalance\n";
echo "Fee Account Balance: $feeAccountBalance\n";
