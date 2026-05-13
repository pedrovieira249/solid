<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Tests\Domain\Service;

use OcpOpenclosedprinciple\Domain\Entity\Arquivo;
use OcpOpenclosedprinciple\Domain\Enum\EnumTipoArquivo;
use OcpOpenclosedprinciple\Domain\Service\ValidarArquivo;
use PHPUnit\Framework\TestCase;

class ValidarArquivoTest extends TestCase
{
    private string $recursos;

    protected function setUp(): void
    {
        $this->recursos = realpath(__DIR__ . '/../../../src/resources');
    }

    public function testNomeComExtensaoRetornaTrue(): void
    {
        $arquivo = new Arquivo('dados.csv', EnumTipoArquivo::CSV, $this->recursos);

        $this->assertTrue((new ValidarArquivo($arquivo))->nome());
    }

    public function testTipoCompativelRetornaTrue(): void
    {
        $arquivo = new Arquivo('dados.csv', EnumTipoArquivo::CSV, $this->recursos);

        $this->assertTrue((new ValidarArquivo($arquivo))->tipo());
    }

    public function testArquivoExistenteRetornaTrue(): void
    {
        $arquivo = new Arquivo('dados.csv', EnumTipoArquivo::CSV, $this->recursos);

        $this->assertTrue((new ValidarArquivo($arquivo))->existe());
    }

    public function testArquivoRemovidoRetornaFalse(): void
    {
        $temp = tempnam(sys_get_temp_dir(), 'ocp_') . '.csv';
        file_put_contents($temp, "nome;cpf\nTeste;123\n");

        $arquivo = new Arquivo(basename($temp), EnumTipoArquivo::CSV, dirname($temp));

        unlink($temp);

        $this->assertFalse((new ValidarArquivo($arquivo))->existe());
    }
}
