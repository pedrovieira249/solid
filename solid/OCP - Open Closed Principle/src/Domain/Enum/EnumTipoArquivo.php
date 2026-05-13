<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Domain\Enum;

enum EnumTipoArquivo
{
    case CSV;
    case XLSX;
    case ODS;
    case XLS;
    case TXT;
}
