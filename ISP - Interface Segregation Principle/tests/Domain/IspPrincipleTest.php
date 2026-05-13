<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Tests\Domain;

use IspInterfacesegregationprinciple\Application\Service\Notificacao;
use IspInterfacesegregationprinciple\Domain\Contract\LogInterface;
use IspInterfacesegregationprinciple\Domain\Contract\NotificacaoInterface;
use IspInterfacesegregationprinciple\Domain\Contract\NotificacaoRecipientInterface;
use IspInterfacesegregationprinciple\Domain\Contract\UsuarioReadInterface;
use IspInterfacesegregationprinciple\Domain\Contract\UsuarioWriteInterface;
use IspInterfacesegregationprinciple\Domain\Entity\Lead;
use IspInterfacesegregationprinciple\Domain\Entity\Usuario;
use IspInterfacesegregationprinciple\Domain\Service\Log;
use PHPUnit\Framework\TestCase;

/**
 * Valida o I do SOLID: nenhuma classe é forçada a implementar métodos
 * que não usa.
 *
 * Interfaces são pequenas e focadas em um único cliente/responsabilidade.
 * Classes dependem apenas dos contratos de que necessitam, recebendo
 * serviços adicionais via injeção de dependência.
 */
class IspPrincipleTest extends TestCase
{
    public function test_log_implementa_log_interface_e_nao_notificacao(): void
    {
        $interfaces = class_implements(Log::class);

        $this->assertArrayHasKey(LogInterface::class, $interfaces);
        $this->assertArrayNotHasKey(NotificacaoInterface::class, $interfaces);
    }

    public function test_notificacao_implementa_notificacao_interface_e_nao_log(): void
    {
        $interfaces = class_implements(Notificacao::class);

        $this->assertArrayHasKey(NotificacaoInterface::class, $interfaces);
        $this->assertArrayNotHasKey(LogInterface::class, $interfaces);
    }

    public function test_lead_nao_implementa_log_interface(): void
    {
        $interfaces = class_implements(Lead::class);

        $this->assertArrayNotHasKey(LogInterface::class, $interfaces);
    }

    public function test_usuario_nao_implementa_log_interface(): void
    {
        $interfaces = class_implements(Usuario::class);

        $this->assertArrayNotHasKey(LogInterface::class, $interfaces);
    }

    public function test_interfaces_de_leitura_e_escrita_do_usuario_sao_distintas(): void
    {
        $this->assertNotSame(UsuarioReadInterface::class, UsuarioWriteInterface::class);
    }

    public function test_interface_de_leitura_nao_contem_metodos_de_escrita(): void
    {
        $readMethods = array_map(
            fn(\ReflectionMethod $m) => $m->name,
            (new \ReflectionClass(UsuarioReadInterface::class))->getMethods()
        );

        $this->assertContains('buscarPorId', $readMethods);
        $this->assertNotContains('salvar', $readMethods);
        $this->assertNotContains('editar', $readMethods);
        $this->assertNotContains('excluir', $readMethods);
        $this->assertNotContains('cadastrarOuAtualizar', $readMethods);
    }

    public function test_interface_de_escrita_nao_contem_metodos_de_leitura(): void
    {
        $writeMethods = array_map(
            fn(\ReflectionMethod $m) => $m->name,
            (new \ReflectionClass(UsuarioWriteInterface::class))->getMethods()
        );

        $this->assertNotContains('buscarPorId', $writeMethods);
        $this->assertContains('salvar', $writeMethods);
        $this->assertContains('editar', $writeMethods);
        $this->assertContains('excluir', $writeMethods);
    }

    public function test_metodos_de_leitura_e_escrita_nao_se_sobrepoem(): void
    {
        $readMethods  = array_map(
            fn(\ReflectionMethod $m) => $m->name,
            (new \ReflectionClass(UsuarioReadInterface::class))->getMethods()
        );
        $writeMethods = array_map(
            fn(\ReflectionMethod $m) => $m->name,
            (new \ReflectionClass(UsuarioWriteInterface::class))->getMethods()
        );

        $this->assertEmpty(array_intersect($readMethods, $writeMethods));
    }

    public function test_notificacao_recipient_interface_tem_apenas_dados_minimos(): void
    {
        $methods = array_map(
            fn(\ReflectionMethod $m) => $m->name,
            (new \ReflectionClass(NotificacaoRecipientInterface::class))->getMethods()
        );

        $this->assertContains('getNome', $methods);
        $this->assertContains('getEmail', $methods);
        $this->assertCount(2, $methods);
    }

    public function test_notificacao_depende_de_recipient_interface_nao_de_usuario_concreto(): void
    {
        $reflection = new \ReflectionClass(Notificacao::class);
        $param      = $reflection->getMethod('enviarNotificacao')->getParameters()[0];

        $this->assertSame(
            NotificacaoRecipientInterface::class,
            $param->getType()->getName()
        );
    }
}
