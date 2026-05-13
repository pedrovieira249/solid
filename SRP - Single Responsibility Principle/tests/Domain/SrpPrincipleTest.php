<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\Contract\EmailServiceInterface;
use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Entity\Cliente;
use SrpSingleresponsibilityprinciple\Domain\Entity\Entregas;
use SrpSingleresponsibilityprinciple\Domain\Entity\Item;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumStatus;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumTipoEntregas;
use SrpSingleresponsibilityprinciple\Domain\Service\CalculadoraPedido;
use SrpSingleresponsibilityprinciple\Domain\Service\CarrinhoItens;
use SrpSingleresponsibilityprinciple\Domain\Service\NotificarPedido;
use SrpSingleresponsibilityprinciple\Domain\Service\StatusDoPedido;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Email;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

/**
 * Valida o S do SOLID: cada classe tem um, e apenas um, motivo para mudar.
 *
 * Cada serviço é testável em isolamento e não produz efeitos colaterais
 * nas responsabilidades das outras classes.
 */
class SrpPrincipleTest extends TestCase
{

    /**
     * StatusDoPedido só gerencia transições de status.
     * Mudar o status NUNCA aciona envio de e-mail.
     */
    #[DataProvider('provedorTransicoesDeStatus')]
    public function test_status_do_pedido_nao_aciona_email_ao_mudar_status(
        EnumStatus $statusEsperado,
        \Closure $transicao
    ): void {
        $emailService = $this->createMock(EmailServiceInterface::class);
        $emailService->expects($this->never())->method('send');

        $carrinho = $this->criarCarrinho();
        $transicao(new StatusDoPedido($carrinho));

        $this->assertSame($statusEsperado, $carrinho->getDadosPedido()->getStatus());
    }

    /**
     * NotificarPedido só envia notificações.
     * Notificar NUNCA altera o status do pedido.
     */
    #[DataProvider('provedorStatusComNotificacao')]
    public function test_notificar_pedido_nao_altera_status(EnumStatus $status): void
    {
        $carrinho     = $this->criarCarrinhoComStatus($status);
        $emailService = $this->createStub(EmailServiceInterface::class);
        $emailService->method('send')->willReturn(true);

        (new NotificarPedido($carrinho, $emailService))->notificar();

        $this->assertSame($status, $carrinho->getDadosPedido()->getStatus());
    }

    /**
     * NotificarPedido envia o e-mail correto para cada status.
     * A responsabilidade está em DECIDIR qual assunto enviar, não em mudar estado.
     */
    #[DataProvider('provedorStatusComAssunto')]
    public function test_notificar_pedido_envia_assunto_correto_para_cada_status(
        EnumStatus $status,
        string $assuntoEsperado
    ): void {
        $carrinho     = $this->criarCarrinhoComStatus($status);
        $emailService = $this->createMock(EmailServiceInterface::class);

        $emailService->expects($this->once())
            ->method('send')
            ->with($this->callback(
                fn(Email $e) => str_contains($e->getAssunto(), $assuntoEsperado)
            ));

        (new NotificarPedido($carrinho, $emailService))->notificar();
    }

    /**
     * CalculadoraPedido só calcula valores.
     * Calcular NUNCA altera status nem aciona e-mail.
     */
    #[DataProvider('provedorItensEValorEsperado')]
    public function test_calculadora_pedido_nao_altera_status_nem_aciona_email(
        array $itens,
        ?Entregas $entrega,
        float $valorEsperado
    ): void {
        $carrinho      = $this->criarCarrinho();
        $statusInicial = $carrinho->getDadosPedido()->getStatus();

        $valor = (new CalculadoraPedido($itens, $entrega))->calcularValorTotal();

        $this->assertSame($valorEsperado, $valor);
        $this->assertSame($statusInicial, $carrinho->getDadosPedido()->getStatus());
    }

    /**
     * CarrinhoItens só gerencia a coleção de itens.
     * Adicionar ou remover itens NUNCA notifica o cliente por e-mail.
     */
    public function test_carrinho_itens_nao_notifica_email_ao_adicionar_item(): void
    {
        $carrinho = $this->criarCarrinho();
        $item     = new Item(1, 'Produto', 2, 50.0);

        $carrinhoItens = new CarrinhoItens($carrinho);
        $carrinhoItens->addItem($item);

        $this->assertCount(1, $carrinho->getDadosPedido()->getItens());
        $this->assertSame(100.0, $carrinho->getDadosPedido()->getValorTotal());
    }

    /**
     * CarrinhoItens delega o cálculo do valor total à CalculadoraPedido.
     * Ele não calcula por si só — respeita o SRP.
     */
    public function test_carrinho_itens_delega_calculo_a_calculadora_pedido(): void
    {
        $carrinho      = $this->criarCarrinho();
        $carrinhoItens = new CarrinhoItens($carrinho);

        $carrinhoItens->addItem(new Item(1, 'Produto A', 3, 20.0));
        $carrinhoItens->addItem(new Item(2, 'Produto B', 1, 40.0));

        $this->assertSame(100.0, $carrinho->getDadosPedido()->getValorTotal());
    }

    public static function provedorTransicoesDeStatus(): array
    {
        return [
            'ABERTO → PENDENTE'   => [EnumStatus::PENDENTE,   fn(StatusDoPedido $s) => $s->pendenciar()],
            'ABERTO → FINALIZADO' => [EnumStatus::FINALIZADO,  fn(StatusDoPedido $s) => $s->finalizar()],
            'ABERTO → CANCELADO'  => [EnumStatus::CANCELADO,   fn(StatusDoPedido $s) => $s->cancelar()],
        ];
    }

    public static function provedorStatusComNotificacao(): array
    {
        return [
            'PENDENTE'   => [EnumStatus::PENDENTE],
            'FINALIZADO' => [EnumStatus::FINALIZADO],
            'CANCELADO'  => [EnumStatus::CANCELADO],
        ];
    }

    public static function provedorStatusComAssunto(): array
    {
        return [
            'PENDENTE envia assunto Pendente'     => [EnumStatus::PENDENTE,   'Pendente'],
            'FINALIZADO envia assunto Finalizado' => [EnumStatus::FINALIZADO,  'Finalizado'],
            'CANCELADO envia assunto Cancelado'   => [EnumStatus::CANCELADO,   'Cancelado'],
        ];
    }

    public static function provedorItensEValorEsperado(): array
    {
        $endereco = new Endereco('Rua A', 1, 'Bairro', 'Cidade', 'Estado', '00000-000');
        $entrega  = new Entregas(EnumTipoEntregas::NORMAL, $endereco, 10);

        return [
            'dois itens sem entrega'    => [[new Item(1, 'P1', 2, 50.0), new Item(2, 'P2', 1, 20.0)], null, 120.0],
            'um item com entrega R$10'  => [[new Item(1, 'P1', 1, 100.0)], $entrega, 110.0],
            'lista vazia sem entrega'   => [[], null, 0.0],
        ];
    }

    private function criarCarrinho(): Carrinho
    {
        $endereco = new Endereco('Rua A', 1, 'Bairro', 'Cidade', 'Estado', '00000-000');
        $cliente  = new Cliente(1, 'João', 'joao@example.com', '11999999999', $endereco);

        return new Carrinho($cliente);
    }

    private function criarCarrinhoComStatus(EnumStatus $status): Carrinho
    {
        $carrinho = $this->criarCarrinho();
        $carrinho->getDadosPedido()->setStatus($status);

        return $carrinho;
    }
}
