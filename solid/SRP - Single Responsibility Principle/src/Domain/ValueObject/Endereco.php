<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\ValueObject;

final class Endereco
{
    public function __construct(
        private readonly string $rua,
        private readonly int $numero,
        private readonly string $bairro,
        private readonly string $cidade,
        private readonly string $estado,
        private readonly string $cep,
        private readonly ?string $complemento = null
    ) {
        if (empty($rua) || empty($bairro) || empty($cidade) || empty($estado) || empty($cep)) {
            throw new \InvalidArgumentException('Todos os campos obrigatórios do endereço devem ser preenchidos.');
        }
    }

    public function getRua(): string
    {
        return $this->rua;
    }

    public function getNumero(): int
    {
        return $this->numero;
    }

    public function getBairro(): string
    {
        return $this->bairro;
    }

    public function getCidade(): string
    {
        return $this->cidade;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getCep(): string
    {
        return $this->cep;
    }

    public function getComplemento(): ?string
    {
        return $this->complemento;
    }
}
