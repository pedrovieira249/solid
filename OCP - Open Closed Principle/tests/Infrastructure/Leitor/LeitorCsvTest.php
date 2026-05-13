<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Tests\Infrastructure\Leitor;

use OcpOpenclosedprinciple\Infrastructure\Leitor\LeitorCsv;
use PHPUnit\Framework\TestCase;

class LeitorCsvTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'ocp_csv_') . '.csv';
        file_put_contents($this->tempFile, "nome;cpf;email\r\nFernanda Silva;405.986.210-08;fernanda.silva@contato.com.br\r\nPedro Vieira;216.474.450-00;angus.pedro@hotmail.com\r\n");
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testRetornaArrayAssociativo(): void
    {
        $dados = (new LeitorCsv())->ler($this->tempFile);

        $this->assertIsArray($dados);
        $this->assertArrayHasKey('nome', $dados[0]);
        $this->assertArrayHasKey('cpf', $dados[0]);
        $this->assertArrayHasKey('email', $dados[0]);
    }

    public function testPrimeiraLinhaUsadaComoCabecalho(): void
    {
        $dados = (new LeitorCsv())->ler($this->tempFile);

        $this->assertCount(2, $dados);
    }

    public function testValoresLidosCorretamente(): void
    {
        $dados = (new LeitorCsv())->ler($this->tempFile);

        $this->assertSame('Fernanda Silva', $dados[0]['nome']);
        $this->assertSame('405.986.210-08', $dados[0]['cpf']);
        $this->assertSame('fernanda.silva@contato.com.br', $dados[0]['email']);
    }

    public function testSegundaLinhaLidaCorretamente(): void
    {
        $dados = (new LeitorCsv())->ler($this->tempFile);

        $this->assertSame('Pedro Vieira', $dados[1]['nome']);
        $this->assertSame('216.474.450-00', $dados[1]['cpf']);
    }

    public function testSuportaSeparadorCustomizado(): void
    {
        $tempComVirgula = tempnam(sys_get_temp_dir(), 'ocp_csv_') . '.csv';
        file_put_contents($tempComVirgula, "nome,cpf,email\nTeste,123.456.789-00,teste@teste.com\n");

        $dados = (new LeitorCsv(','))->ler($tempComVirgula);

        unlink($tempComVirgula);

        $this->assertSame('Teste', $dados[0]['nome']);
        $this->assertSame('123.456.789-00', $dados[0]['cpf']);
    }
}
