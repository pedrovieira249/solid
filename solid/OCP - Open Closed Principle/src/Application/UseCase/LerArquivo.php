<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Application\UseCase;

use OcpOpenclosedprinciple\Domain\Entity\Arquivo;
use OcpOpenclosedprinciple\Domain\Service\LeitorArquivoInterface;

final class LerArquivo
{
    public function __construct(
        private LeitorArquivoInterface $leitor
    ) {}

    public function execute(Arquivo $arquivo): array
    {
        return $this->leitor->ler($arquivo->getCaminhoCompleto());
    }
}
