<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Domain\Service;

interface LeitorArquivoInterface
{
    public function ler(string $caminho): array;
}
