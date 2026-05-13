<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Domain\Contract;

interface LogInterface
{
    public function registrarLog(string $message): void;
}
