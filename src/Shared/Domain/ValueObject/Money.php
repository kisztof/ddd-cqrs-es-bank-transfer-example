<?php

namespace Kisztof\BankTransferExample\Shared\Domain\ValueObject;

final readonly class Money
{
    public int $amount;
    public Currency $currency;

    public function __construct(int $amount, Currency $currency)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative.');
        }
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function add(Money $money): self
    {
        $this->assertSameCurrency($money);
        return new self($this->amount + $money->amount, $this->currency);
    }

    public function subtract(Money $money): self
    {
        $this->assertSameCurrency($money);
        $newAmount = $this->amount - $money->amount;
        if ($newAmount < 0) {
            throw new \DomainException('Insufficient funds.');
        }
        return new self($newAmount, $this->currency);
    }

    private function assertSameCurrency(Money $money): void
    {
        if (!$this->currency->equals($money->currency)) {
            throw new \InvalidArgumentException('Currency mismatch');
        }
    }
}
