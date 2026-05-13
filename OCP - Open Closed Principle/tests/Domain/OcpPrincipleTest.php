<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Tests\Domain;

use OcpOpenclosedprinciple\Application\UseCase\LerArquivo;
use OcpOpenclosedprinciple\Domain\Entity\Arquivo;
use OcpOpenclosedprinciple\Domain\Enum\EnumTipoArquivo;
use OcpOpenclosedprinciple\Domain\Service\LeitorArquivoInterface;
use OcpOpenclosedprinciple\Infrastructure\Leitor\LeitorArquivoFactory;
use OcpOpenclosedprinciple\Infrastructure\Leitor\LeitorCsv;
use OcpOpenclosedprinciple\Infrastructure\Leitor\LeitorTxt;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Valida o O do SOLID: o use case LerArquivo é FECHADO para modificação,
 * mas ABERTO para extensão — novos leitores são adicionados sem alterar nenhuma
 * classe existente.
 */
class OcpPrincipleTest extends TestCase
{
    /**
     * Cada implementação de LeitorArquivoInterface deve honrar o contrato:
     * receber um caminho e retornar um array de dados.
     */
    #[DataProvider('provedorLeitoresConcretos')]
    public function test_cada_leitor_implementa_a_interface(string $leitorClasse): void
    {
        $interfaces = class_implements($leitorClasse);

        $this->assertArrayHasKey(LeitorArquivoInterface::class, $interfaces);
    }

    /**
     * LerArquivo nunca precisa ser alterado para suportar um novo leitor.
     * Basta criar uma nova classe implementando LeitorArquivoInterface.
     */
    public function test_ler_arquivo_aceita_qualquer_implementacao_sem_ser_modificado(): void
    {
        $leitor = $this->createStub(LeitorArquivoInterface::class);
        $leitor->method('ler')->willReturn([['campo' => 'valor']]);

        $useCase = new LerArquivo($leitor);

        $tempFile = tempnam(sys_get_temp_dir(), 'ocp_') . '.csv';
        file_put_contents($tempFile, "campo\nvalor\n");
        $arquivo = new Arquivo(basename($tempFile), EnumTipoArquivo::CSV, dirname($tempFile));

        $dados = $useCase->execute($arquivo);

        unlink($tempFile);

        $this->assertSame([['campo' => 'valor']], $dados);
    }

    /**
     * Demonstra extensão via classe anônima: um leitor totalmente novo funciona
     * através de LerArquivo sem que NENHUMA classe existente seja alterada.
     */
    public function test_novo_leitor_customizado_funciona_sem_alterar_classes_existentes(): void
    {
        $novoLeitor = new class implements LeitorArquivoInterface {
            public function ler(string $caminho): array
            {
                return [['formato' => 'novo', 'dado' => 'extensao']];
            }
        };

        $tempFile = tempnam(sys_get_temp_dir(), 'ocp_') . '.csv';
        file_put_contents($tempFile, "conteudo\n");
        $arquivo = new Arquivo(basename($tempFile), EnumTipoArquivo::CSV, dirname($tempFile));

        $useCase = new LerArquivo($novoLeitor);
        $dados   = $useCase->execute($arquivo);

        unlink($tempFile);

        $this->assertSame([['formato' => 'novo', 'dado' => 'extensao']], $dados);
    }

    /**
     * A factory também respeita o OCP: adicionar um novo leitor não exige alterar
     * LeitorArquivoFactory — basta criar a classe com a nomenclatura correta.
     */
    #[DataProvider('provedorArquivosReais')]
    public function test_factory_resolve_leitor_por_convencao_sem_ser_alterada(
        Arquivo $arquivo,
        string $leitorEsperado
    ): void {
        $leitor = LeitorArquivoFactory::criar($arquivo);

        $this->assertInstanceOf($leitorEsperado, $leitor);
    }

    /**
     * Qualquer leitor retorna um array — o contrato do domínio é honrado por todos.
     */
    #[DataProvider('provedorArquivosReais')]
    public function test_todo_leitor_retorna_array_ao_ser_usado_pelo_use_case(Arquivo $arquivo, string $leitorEsperado): void
    {
        $leitor  = LeitorArquivoFactory::criar($arquivo);
        $useCase = new LerArquivo($leitor);

        $dados = $useCase->execute($arquivo);

        $this->assertIsArray($dados);
        $this->assertNotEmpty($dados);
    }

    public static function provedorLeitoresConcretos(): array
    {
        return [
            'LeitorCsv' => [LeitorCsv::class],
            'LeitorTxt' => [LeitorTxt::class],
        ];
    }

    public static function provedorArquivosReais(): array
    {
        $recursos = realpath(__DIR__ . '/../../src/resources');

        return [
            'CSV' => [
                new Arquivo('dados.csv', EnumTipoArquivo::CSV, $recursos),
                LeitorCsv::class,
            ],
            'TXT' => [
                new Arquivo('dados.txt', EnumTipoArquivo::TXT, $recursos),
                LeitorTxt::class,
            ],
        ];
    }
}
