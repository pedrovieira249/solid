<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\ValueObject;

class Email
{
    public function __construct(
        private readonly string $email,
        private readonly string $assunto,
        private readonly string $mensagem
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAssunto(): string
    {
        return $this->assunto;
    }

    public function getMensagem(): string
    {
        return $this->mensagem;
    }
}
