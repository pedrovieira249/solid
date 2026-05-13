<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Domain\Service;

use IspInterfacesegregationprinciple\Domain\Contract\LogInterface;

class Log implements LogInterface
{
    public function registrarLog(string $message): void
    {
        echo "Log registrado: " . $message . "\n";
    }
}
