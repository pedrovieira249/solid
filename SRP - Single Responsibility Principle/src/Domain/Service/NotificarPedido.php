<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Service;

use SrpSingleresponsibilityprinciple\Domain\Contract\EmailServiceInterface;
use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Email;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumStatus;

final class NotificarPedido
{
    public function __construct(
        private Carrinho $carrinho,
        private EmailServiceInterface $emailService,
    ) {
        if ($this->carrinho->getCliente() === null) {
            throw new \InvalidArgumentException('O carrinho deve conter um cliente para ser notificado.');
        }
    }

    public function notificar(): void
    {
        $status = $this->carrinho->getDadosPedido()->getStatus();

        match ($status) {
            EnumStatus::PENDENTE => $this->enviarEmailPedido('Pedido Pendente', 'Seu pedido está pendente. Por favor, aguarde a confirmação.'),
            EnumStatus::FINALIZADO => $this->enviarEmailPedido('Pedido Finalizado', 'Seu pedido foi finalizado com sucesso. Obrigado pela compra!'),
            EnumStatus::CANCELADO => $this->enviarEmailPedido('Pedido Cancelado', 'Seu pedido foi cancelado. Se tiver alguma dúvida, entre em contato conosco.'),
            default => null,
        };
    }

    private function enviarEmailPedido(string $assunto, string $mensagem): void
    {
        $this->emailService->send(new Email($this->carrinho->getCliente()->getEmail(), $assunto, $mensagem));
    }
}
