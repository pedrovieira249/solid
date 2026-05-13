<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Infrastructure\Leitor;

use OcpOpenclosedprinciple\Domain\Entity\Arquivo;
use OcpOpenclosedprinciple\Domain\Service\LeitorArquivoInterface;

class LeitorArquivoFactory
{
    private const NAMESPACE = 'OcpOpenclosedprinciple\\Infrastructure\\Leitor\\';

    public static function criar(Arquivo $arquivo): LeitorArquivoInterface
    {
        $tipo   = ucfirst(strtolower($arquivo->getTipo()->name));
        $classe = self::NAMESPACE . 'Leitor' . $tipo;

        if (!class_exists($classe)) {
            throw new \InvalidArgumentException(
                "Tipo de arquivo '{$tipo}' não possui leitor implementado."
            );
        }

        return new $classe();
    }
}

