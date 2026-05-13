<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Application\UseCase;

use SrpSingleresponsibilityprinciple\Domain\Contract\EmailServiceInterface;
use SrpSingleresponsibilityprinciple\Domain\Entity\Pedido;
use SrpSingleresponsibilityprinciple\Domain\Service\NotificarPedido;
use SrpSingleresponsibilityprinciple\Domain\Service\StatusDoPedido;

final class FinalizarPedidoUseCase
{
    public function __construct(
        private EmailServiceInterface $emailService,
    ) {}

    public function executar(Pedido $pedido): void
    {
        $carrinho = $pedido->getCarrinho();

        (new StatusDoPedido($carrinho))->finalizar();
        (new NotificarPedido($carrinho, $this->emailService))->notificar();
    }
}
