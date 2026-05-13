<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Application\Service;

use IspInterfacesegregationprinciple\Domain\Contract\NotificacaoInterface;
use IspInterfacesegregationprinciple\Domain\Contract\NotificacaoRecipientInterface;

class Notificacao implements NotificacaoInterface
{
    public function enviarNotificacao(NotificacaoRecipientInterface $recipient, string $mensagem): bool
    {
        echo "Enviando notificação para '{$recipient->getNome()}' ({$recipient->getEmail()}): {$mensagem}\n";
        return true;
    }
}
