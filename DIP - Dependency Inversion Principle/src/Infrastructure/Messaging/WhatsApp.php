<?php

declare(strict_types=1);

namespace DipDependencyInversionPrinciple\Infrastructure\Messaging;

use DipDependencyInversionPrinciple\Domain\Contract\MensageiroInterface;

final class WhatsApp implements MensageiroInterface
{
    public function enviarMensagem(string $contato, string $mensagem): bool
    {
        echo "Enviando mensagem para {$contato} via WhatsApp: {$mensagem}\n";
        return true;
    }

    public function enviarToken(string $contato, string $token): bool
    {
        echo "Enviando token para {$contato} via WhatsApp: {$token}\n";
        return true;
    }
}
