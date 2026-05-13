<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Domain\Contract;

interface UsuarioReadInterface
{
    public function buscarPorId(): static|null;
}
