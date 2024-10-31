<?php

namespace Kisztof\BankTransferExample\Shared\Domain\ValueObject;

final readonly class Currency
{
    public string $code;

    public function __construct(string $code)
    {
        $this->code = strtoupper($code);
    }

    public function equals(Currency $currency): bool
    {
        return $this->code === $currency->code;
    }
}
