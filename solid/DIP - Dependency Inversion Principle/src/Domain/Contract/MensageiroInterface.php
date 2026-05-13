<?php

declare(strict_types=1);

namespace DipDependencyInversionPrinciple\Domain\Contract;

interface MensageiroInterface
{
    public function enviarMensagem(string $contato, string $mensagem): bool;
    public function enviarToken(string $contato, string $token): bool;
}
