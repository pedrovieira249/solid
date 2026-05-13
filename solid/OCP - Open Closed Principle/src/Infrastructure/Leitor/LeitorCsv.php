<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Infrastructure\Leitor;

use OcpOpenclosedprinciple\Domain\Service\LeitorArquivoInterface;

class LeitorCsv implements LeitorArquivoInterface
{
    public function __construct(private string $separador = ';') {}

    public function ler(string $caminho): array
    {
        $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $cabecalho = str_getcsv(rtrim(array_shift($linhas), "\r"), $this->separador);

        return array_map(
            fn(string $linha) => array_combine($cabecalho, str_getcsv(rtrim($linha, "\r"), $this->separador)),
            $linhas
        );
    }
}
