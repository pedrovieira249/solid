<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SrpSingleresponsibilityprinciple\Application\UseCase\CriarPedidoUseCase;
use SrpSingleresponsibilityprinciple\Application\UseCase\FinalizarPedidoUseCase;
use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Entity\Cliente;
use SrpSingleresponsibilityprinciple\Domain\Entity\Item;
use SrpSingleresponsibilityprinciple\Domain\Service\CarrinhoItens;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;
use SrpSingleresponsibilityprinciple\Infrastructure\Service\EmailService;

$emailService  = new EmailService();
$endereco      = new Endereco('Rua A', 123, 'Bairro B', 'Cidade C', 'Estado D', '12345-678');
$cliente       = new Cliente(1, 'João', 'joao@example.com', '12345678900', $endereco);
$carrinho      = new Carrinho($cliente);
$item1         = new Item(1, 'Produto 1', 2, 50.0);
$item2         = new Item(2, 'Produto 2', 1, 20.0);
$item3         = new Item(3, 'Produto 3', 4, 100.0);
$item4         = new Item(4, 'Produto 4', 5, 20.0);

$carrinhoItens = new CarrinhoItens($carrinho);
$carrinhoItens->addItem($item1);
$carrinhoItens->addItem($item2);
$carrinhoItens->addItem($item3);
$carrinhoItens->addItem($item4);
$carrinhoItens->removerItem($item4);

echo '<pre>';
var_dump($carrinho);
echo '</pre>';

$pedido = (new CriarPedidoUseCase($emailService))->executar(1, $carrinho);
(new FinalizarPedidoUseCase($emailService))->executar($pedido);

echo '<pre>';
var_dump($pedido);
echo '</pre>';
