<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Domain\Service;

use OcpOpenclosedprinciple\Domain\Entity\Arquivo;

final class ValidarArquivo
{
    public function __construct(
        private Arquivo $arquivo
    )
    {}

    public function nome(): bool
    {
        $nome = $this->arquivo->getNome();

        if (str_contains($nome, '.')) {
            return true;
        }

        return false;
    }

    public function tipo(): bool
    {
        $nome = $this->arquivo->getNome();
        $tipo = $this->arquivo->getTipo();

        if ($this->nome()) {
            $extensao = pathinfo($nome, PATHINFO_EXTENSION);
            if ($extensao === strtolower($tipo->name)) {
                return true;
            }
        }

        return false;
    }

    public function existe(): bool
    {
        return file_exists($this->arquivo->getCaminhoCompleto());
    }
}
