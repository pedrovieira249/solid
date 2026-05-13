<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Domain\Entity;

use IspInterfacesegregationprinciple\Infrastructure\Database\DBConnection;
use IspInterfacesegregationprinciple\Domain\Contract\ContratoInterface;

class Contrato extends DBConnection implements ContratoInterface
{
    public function salvar(): bool
    {
        echo "Contrato salvo no banco de dados.\n";
        return true;
    }
}
