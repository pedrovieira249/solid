<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Domain\Contract;

interface NotificacaoRecipientInterface
{
    public function getNome(): string;
    public function getEmail(): string;
}
