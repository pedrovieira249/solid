<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Domain\Entity;

use OcpOpenclosedprinciple\Domain\Enum\EnumTipoArquivo;
use OcpOpenclosedprinciple\Domain\Service\ValidarArquivo;

final class Arquivo
{
    public function __construct(
        private string $nome,
        private EnumTipoArquivo $tipo,
        private string $diretorio
    ){
        $validarArquivo = new ValidarArquivo($this);
        if (!$validarArquivo->nome()) {
            throw new \InvalidArgumentException("O nome do arquivo deve conter uma extensão. Exemplo: 'dados.csv'.");
        }

        if (!$validarArquivo->tipo()) {
            throw new \InvalidArgumentException("A extensão do arquivo deve ser compatível com o tipo especificado.");
        }

        if (!$validarArquivo->existe()) {
            throw new \InvalidArgumentException("O arquivo '{$this->getCaminhoCompleto()}' não foi encontrado.");
        }
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDiretorio(): string
    {
        return $this->diretorio;
    }

    public function getTipo(): EnumTipoArquivo
    {
        return $this->tipo;
    }

    public function getCaminhoCompleto(): string
    {
        return str_replace('//', DIRECTORY_SEPARATOR, $this->diretorio . DIRECTORY_SEPARATOR . $this->nome);
    }
}
