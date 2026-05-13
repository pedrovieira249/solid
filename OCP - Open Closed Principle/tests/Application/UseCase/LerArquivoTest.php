<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Tests\Application\UseCase;

use OcpOpenclosedprinciple\Application\UseCase\LerArquivo;
use OcpOpenclosedprinciple\Domain\Entity\Arquivo;
use OcpOpenclosedprinciple\Domain\Enum\EnumTipoArquivo;
use OcpOpenclosedprinciple\Domain\Service\LeitorArquivoInterface;
use OcpOpenclosedprinciple\Infrastructure\Leitor\LeitorArquivoFactory;
use PHPUnit\Framework\TestCase;

class LerArquivoTest extends TestCase
{
    private string $recursos;

    protected function setUp(): void
    {
        $this->recursos = realpath(__DIR__ . '/../../../src/resources');
    }

    public function testRetornaDadosCsv(): void
    {
        $arquivo    = new Arquivo('dados.csv', EnumTipoArquivo::CSV, $this->recursos);
        $useCase    = new LerArquivo(LeitorArquivoFactory::criar($arquivo));

        $dados = $useCase->execute($arquivo);

        $this->assertNotEmpty($dados);
    }

    public function testDadosCsvSaoAssociativos(): void
    {
        $arquivo = new Arquivo('dados.csv', EnumTipoArquivo::CSV, $this->recursos);
        $useCase = new LerArquivo(LeitorArquivoFactory::criar($arquivo));

        $primeira = $useCase->execute($arquivo)[0];

        $this->assertArrayHasKey('nome', $primeira);
        $this->assertArrayHasKey('cpf', $primeira);
        $this->assertArrayHasKey('email', $primeira);
    }

    public function testRetornaDadosTxt(): void
    {
        $arquivo = new Arquivo('dados.txt', EnumTipoArquivo::TXT, $this->recursos);
        $useCase = new LerArquivo(LeitorArquivoFactory::criar($arquivo));

        $dados = $useCase->execute($arquivo);

        $this->assertNotEmpty($dados);
    }

    public function testDadosTxtSaoAssociativos(): void
    {
        $arquivo  = new Arquivo('dados.txt', EnumTipoArquivo::TXT, $this->recursos);
        $useCase  = new LerArquivo(LeitorArquivoFactory::criar($arquivo));

        $primeira = $useCase->execute($arquivo)[0];

        $this->assertArrayHasKey('nome', $primeira);
        $this->assertArrayHasKey('cpf', $primeira);
        $this->assertArrayHasKey('email', $primeira);
    }

    public function testAceitaQualquerImplementacaoDoContrato(): void
    {
        $arquivo = new Arquivo('dados.csv', EnumTipoArquivo::CSV, $this->recursos);

        $leitorCustom = new class implements LeitorArquivoInterface {
            public function ler(string $caminho): array
            {
                return [['nome' => 'Teste', 'cpf' => '000.000.000-00', 'email' => 'teste@test.com']];
            }
        };

        $dados = (new LerArquivo($leitorCustom))->execute($arquivo);

        $this->assertSame('Teste', $dados[0]['nome']);
    }
}
