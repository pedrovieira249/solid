<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use LspLiskovsubstitutionprinciple\Domain\ValueObject\Quadrado;
use LspLiskovsubstitutionprinciple\Domain\ValueObject\Retangulo;
use LspLiskovsubstitutionprinciple\Domain\ValueObject\Paralelogramo;

$retangulo     = new Retangulo(5, 3);
$quadrado      = new Quadrado(4);
$paralelogramo = new Paralelogramo(6, 4);

echo "Área do retângulo: " . $retangulo->calcularArea() . PHP_EOL;
echo "Área do quadrado: " . $quadrado->calcularArea() . PHP_EOL;
echo "Área do paralelogramo: " . $paralelogramo->calcularArea() . PHP_EOL;
