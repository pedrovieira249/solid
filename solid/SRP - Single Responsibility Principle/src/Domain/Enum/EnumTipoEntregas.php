<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Enum;

enum EnumTipoEntregas
{
    case RETIRADA_LOJA;
    case NORMAL;
    case EXPRESSA;
    case TURBO;
}
