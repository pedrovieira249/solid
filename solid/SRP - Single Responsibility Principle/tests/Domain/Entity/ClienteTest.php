<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\Entity\Cliente;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class ClienteTest extends TestCase
{
    private function criarEndereco(): Endereco
    {
        return new Endereco('Rua A', 123, 'Bairro B', 'Cidade C', 'Estado D', '12345-678');
    }

    public function testGettersRetornamValoresCorretos(): void
    {
        $endereco = $this->criarEndereco();
        $cliente  = new Cliente(1, 'João', 'joao@example.com', '11999999999', $endereco);

        $this->assertSame(1, $cliente->getId());
        $this->assertSame('João', $cliente->getNome());
        $this->assertSame('joao@example.com', $cliente->getEmail());
        $this->assertSame('11999999999', $cliente->getTelefone());
        $this->assertSame($endereco, $cliente->getEndereco());
    }

    public function testEnderecoNullPermitido(): void
    {
        $cliente = new Cliente(2, 'Maria', 'maria@example.com', '11988888888', null);

        $this->assertNull($cliente->getEndereco());
    }

    public function testCadastrarEnderecoAtualizaEndereco(): void
    {
        $cliente        = new Cliente(3, 'Carlos', 'carlos@example.com', '11977777777', null);
        $novoEndereco   = $this->criarEndereco();

        $cliente->cadastrarEndereco($novoEndereco);

        $this->assertSame($novoEndereco, $cliente->getEndereco());
    }
}
