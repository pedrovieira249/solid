<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use DipDependencyInversionPrinciple\Domain\Service\Mensageiro;
use DipDependencyInversionPrinciple\Infrastructure\Messaging\Email;
use DipDependencyInversionPrinciple\Infrastructure\Messaging\Sms;
use DipDependencyInversionPrinciple\Infrastructure\Messaging\WhatsApp;

$mensageiroEmail    = new Mensageiro(new Email());
$mensageiroSms      = new Mensageiro(new Sms());
$mensageiroWhatsApp = new Mensageiro(new WhatsApp());

echo '<pre>';
echo "Enviando mensagens via Email:\n";
print_r($mensageiroEmail->enviarMensagem('contato@example.com', 'Olá, esta é uma mensagem de teste email.'));
print_r($mensageiroEmail->enviarToken('contato@example.com', '87678'));
echo '</pre>';

echo '<pre>';
echo "Enviando mensagens via SMS:\n";
print_r($mensageiroSms->enviarMensagem('contato1@example.com', 'Olá, esta é uma mensagem de teste sms.'));
print_r($mensageiroSms->enviarToken('5531987654321', '57461'));
echo '</pre>';

echo '<pre>';
echo "Enviando mensagens via WhatsApp:\n";
print_r($mensageiroWhatsApp->enviarMensagem('contato2@example.com', 'Olá, esta é uma mensagem de teste whatsapp.'));
print_r($mensageiroWhatsApp->enviarToken('5531987654322', '76451'));
echo '</pre>';
