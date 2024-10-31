<?php

namespace Kisztof\BankTransferExample\Shared\Domain\ValueObject;

use Ramsey\Uuid\Uuid;

abstract class AbstractId
{
    private readonly string $value;

    public function __construct(string $id = null)
    {
        $this->value = $id ?: Uuid::uuid4()->toString();
    }

    public static function fromString(string $value): self
    {
        return new static($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(AbstractId $id): bool
    {
        return $id instanceof self && $this->value === $id->value;
    }
}
