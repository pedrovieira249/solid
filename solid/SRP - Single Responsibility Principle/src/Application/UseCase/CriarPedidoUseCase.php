<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Application\UseCase;

use SrpSingleresponsibilityprinciple\Domain\Contract\EmailServiceInterface;
use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Entity\Pedido;
use SrpSingleresponsibilityprinciple\Domain\Service\NotificarPedido;
use SrpSingleresponsibilityprinciple\Domain\Service\StatusDoPedido;

final class CriarPedidoUseCase
{
    public function __construct(
        private EmailServiceInterface $emailService,
    ) {}

    public function executar(int $idPedido, Carrinho $carrinho): Pedido
    {
        $pedido = new Pedido($idPedido, $carrinho);

        (new StatusDoPedido($carrinho))->pendenciar();
        (new NotificarPedido($carrinho, $this->emailService))->notificar();

        return $pedido;
    }
}
