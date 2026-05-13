<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Tests\Infrastructure\Leitor;

use OcpOpenclosedprinciple\Domain\Entity\Arquivo;
use OcpOpenclosedprinciple\Domain\Enum\EnumTipoArquivo;
use OcpOpenclosedprinciple\Infrastructure\Leitor\LeitorArquivoFactory;
use OcpOpenclosedprinciple\Infrastructure\Leitor\LeitorCsv;
use OcpOpenclosedprinciple\Infrastructure\Leitor\LeitorTxt;
use PHPUnit\Framework\TestCase;

class LeitorArquivoFactoryTest extends TestCase
{
    private string $recursos;

    protected function setUp(): void
    {
        $this->recursos = realpath(__DIR__ . '/../../../src/resources');
    }

    public function testCriaLeitorCsvParaTipoCSV(): void
    {
        $arquivo = new Arquivo('dados.csv', EnumTipoArquivo::CSV, $this->recursos);

        $leitor = LeitorArquivoFactory::criar($arquivo);

        $this->assertInstanceOf(LeitorCsv::class, $leitor);
    }

    public function testCriaLeitorTxtParaTipoTXT(): void
    {
        $arquivo = new Arquivo('dados.txt', EnumTipoArquivo::TXT, $this->recursos);

        $leitor = LeitorArquivoFactory::criar($arquivo);

        $this->assertInstanceOf(LeitorTxt::class, $leitor);
    }

    public function testLancaExcecaoParaTipoNaoImplementado(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'ocp_') . '.xlsx';
        file_put_contents($tempFile, 'conteudo');

        $arquivo = new Arquivo(basename($tempFile), EnumTipoArquivo::XLSX, dirname($tempFile));

        unlink($tempFile);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("não possui leitor implementado");

        LeitorArquivoFactory::criar($arquivo);
    }
}
