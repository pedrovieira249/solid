<?php

declare(strict_types=1);

namespace DipDependencyInversionPrinciple\Domain\Service;

use DipDependencyInversionPrinciple\Domain\Contract\MensageiroInterface;

final class Mensageiro
{
    public function __construct(
        private MensageiroInterface $canal
    ){}

    public function enviarMensagem(string $contato, string $mensagem): bool
    {
        return $this->canal->enviarMensagem($contato, $mensagem);
    }

    public function enviarToken(string $contato, string $token): bool
    {
        return $this->canal->enviarToken($contato, $token);
    }
}
