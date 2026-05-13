<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use IspInterfacesegregationprinciple\Application\Service\Notificacao;
use IspInterfacesegregationprinciple\Domain\Entity\Contrato;
use IspInterfacesegregationprinciple\Domain\Entity\Lead;
use IspInterfacesegregationprinciple\Domain\Entity\Usuario;
use IspInterfacesegregationprinciple\Domain\Service\Log;

$log        = new Log();
$notificacao = new Notificacao();

$lead     = new Lead('Maria', 'maria@example.com', $log);
$contrato = new Contrato();
$usuario  = new Usuario(1, 'João', 'joao@example.com', $log, $notificacao);

echo '<pre>';
echo "Gerando Lead:\n";
print_r($lead);
$lead->gerar();
echo '</pre></br>';

echo '<pre>';
echo "Usuario:\n";
print_r($usuario);
echo $usuario->getId(), "\n";
echo $usuario->getNome(), "\n";
echo $usuario->getEmail(), "\n";
$usuario->registrarLog("Usuário logado.");
$usuario->enviarNotificacao("Bem-vindo, {$usuario->getNome()}!");
echo '</pre>';

echo '<pre></br>';
print_r($contrato);
$contrato->salvar();
echo '</pre><br>';
