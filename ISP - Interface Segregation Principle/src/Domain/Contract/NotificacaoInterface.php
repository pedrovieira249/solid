<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Domain\Contract;

interface NotificacaoInterface
{
    public function enviarNotificacao(NotificacaoRecipientInterface $recipient, string $mensagem): bool;
}
