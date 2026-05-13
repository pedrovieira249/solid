<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Enum;

enum EnumStatus
{
    case ABERTO;
    case PENDENTE;
    case FINALIZADO;
    case CANCELADO;
}
