<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Entity;

use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

final class Cliente
{
    public function __construct(
        private readonly int $id,
        private string $nome,
        private string $email,
        private string $telefone,
        private ?Endereco $endereco
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelefone(): string
    {
        return $this->telefone;
    }

    public function getEndereco(): ?Endereco
    {
        return $this->endereco;
    }

    public function cadastrarEndereco(Endereco $endereco): void
    {
        $this->endereco = $endereco;
    }
}
