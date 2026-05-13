<?php

declare(strict_types=1);

namespace DipDependencyInversionPrinciple\Infrastructure\Messaging;

use DipDependencyInversionPrinciple\Domain\Contract\MensageiroInterface;

final class Sms implements MensageiroInterface
{
    public function enviarMensagem(string $contato, string $mensagem): bool
    {
        echo "Enviando mensagem para {$contato} via SMS: {$mensagem}\n";
        return true;
    }

    public function enviarToken(string $contato, string $token): bool
    {
        echo "Enviando token para {$contato} via SMS: {$token}\n";
        return true;
    }
}
