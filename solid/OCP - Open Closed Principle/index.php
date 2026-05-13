<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use OcpOpenclosedprinciple\Application\UseCase\LerArquivo;
use OcpOpenclosedprinciple\Domain\Entity\Arquivo;
use OcpOpenclosedprinciple\Domain\Enum\EnumTipoArquivo;
use OcpOpenclosedprinciple\Infrastructure\Leitor\LeitorArquivoFactory;

$arquivo  = new Arquivo('dados.csv', EnumTipoArquivo::CSV, __DIR__ . '/src/resources/');
$arquivo1 = new Arquivo('dados.txt', EnumTipoArquivo::TXT, __DIR__ . '/src/resources/');

$dadosCsv = (new LerArquivo(LeitorArquivoFactory::criar($arquivo)))->execute($arquivo);
$dadosTxt = (new LerArquivo(LeitorArquivoFactory::criar($arquivo1)))->execute($arquivo1);

echo '<pre>';
var_dump($arquivo, $arquivo->getCaminhoCompleto(), $dadosCsv);
echo '</pre>';

echo '<pre>';
var_dump($arquivo1, $arquivo1->getCaminhoCompleto(), $dadosTxt);
echo '</pre>';
