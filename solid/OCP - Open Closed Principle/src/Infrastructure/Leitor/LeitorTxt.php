<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Infrastructure\Leitor;

use OcpOpenclosedprinciple\Domain\Service\LeitorArquivoInterface;

class LeitorTxt implements LeitorArquivoInterface
{
    public function ler(string $caminho): array
    {
        $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return array_map(function (string $linha) {
            preg_match('/^(.+?)(\d{3}\.\d{3}\.\d{3}-\d{2})\s+(\S+)$/', rtrim($linha, "\r"), $partes);

            return [
                'nome'  => trim($partes[1] ?? ''),
                'cpf'   => trim($partes[2] ?? ''),
                'email' => trim($partes[3] ?? ''),
            ];
        }, $linhas);
    }
}
