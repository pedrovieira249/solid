<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\Entity\Item;

class ItemTest extends TestCase
{
    public function testGettersRetornamValoresCorretos(): void
    {
        $item = new Item(1, 'Produto X', 3, 49.90);

        $this->assertSame(1, $item->getIdProduto());
        $this->assertSame('Produto X', $item->getNomeProduto());
        $this->assertSame(3, $item->getQuantidade());
        $this->assertSame(49.90, $item->getValorUnitario());
    }
}
