<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Tests\Infrastructure\Leitor;

use OcpOpenclosedprinciple\Infrastructure\Leitor\LeitorTxt;
use PHPUnit\Framework\TestCase;

class LeitorTxtTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'ocp_txt_') . '.txt';
        file_put_contents(
            $this->tempFile,
            "Fernanda Silva405.986.210-08             fernanda.silva@contato.com.br\r\n" .
            "Pedro Vieira216.474.450-00              angus.pedro@hotmail.com\r\n" .
            "Anderson de Souza988.964.910-10         anderson.souza@contato.com.br\r\n"
        );
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testRetornaArrayAssociativo(): void
    {
        $dados = (new LeitorTxt())->ler($this->tempFile);

        $this->assertIsArray($dados);
        $this->assertArrayHasKey('nome', $dados[0]);
        $this->assertArrayHasKey('cpf', $dados[0]);
        $this->assertArrayHasKey('email', $dados[0]);
    }

    public function testQuantidadeDeRegistros(): void
    {
        $dados = (new LeitorTxt())->ler($this->tempFile);

        $this->assertCount(3, $dados);
    }

    public function testParseiaNomeCpfEmail(): void
    {
        $dados = (new LeitorTxt())->ler($this->tempFile);

        $this->assertSame('Fernanda Silva', $dados[0]['nome']);
        $this->assertSame('405.986.210-08', $dados[0]['cpf']);
        $this->assertSame('fernanda.silva@contato.com.br', $dados[0]['email']);
    }

    public function testNomeCompostoEPreservado(): void
    {
        $dados = (new LeitorTxt())->ler($this->tempFile);

        $this->assertSame('Anderson de Souza', $dados[2]['nome']);
    }
}
