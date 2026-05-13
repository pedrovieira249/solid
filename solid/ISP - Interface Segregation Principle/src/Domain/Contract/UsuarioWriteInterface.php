<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Domain\Contract;

interface UsuarioWriteInterface
{
    public function salvar(): bool;
    public function editar(): bool;
    public function excluir(): bool;
    public function cadastrarOuAtualizar(): static;
}
