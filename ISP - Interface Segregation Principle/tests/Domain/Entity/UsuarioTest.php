<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Tests\Domain\Entity;

use IspInterfacesegregationprinciple\Domain\Contract\LogInterface;
use IspInterfacesegregationprinciple\Domain\Contract\NotificacaoInterface;
use IspInterfacesegregationprinciple\Domain\Contract\NotificacaoRecipientInterface;
use IspInterfacesegregationprinciple\Domain\Contract\UsuarioReadInterface;
use IspInterfacesegregationprinciple\Domain\Contract\UsuarioWriteInterface;
use IspInterfacesegregationprinciple\Domain\Entity\Usuario;
use PHPUnit\Framework\TestCase;

class UsuarioTest extends TestCase
{
    private LogInterface $logger;
    private NotificacaoInterface $notificacao;
    private Usuario $usuario;

    protected function setUp(): void
    {
        $this->logger      = $this->createStub(LogInterface::class);
        $this->notificacao = $this->createStub(NotificacaoInterface::class);

        ob_start();
        $this->usuario = new Usuario(1, 'João', 'joao@example.com', $this->logger, $this->notificacao);
        ob_end_clean();
    }

    public function testImplementaUsuarioWriteInterface(): void
    {
        $this->assertInstanceOf(UsuarioWriteInterface::class, $this->usuario);
    }

    public function testImplementaUsuarioReadInterface(): void
    {
        $this->assertInstanceOf(UsuarioReadInterface::class, $this->usuario);
    }

    public function testImplementaNotificacaoRecipientInterface(): void
    {
        $this->assertInstanceOf(NotificacaoRecipientInterface::class, $this->usuario);
    }

    public function testNaoImplementaLogInterface(): void
    {
        $this->assertNotInstanceOf(LogInterface::class, $this->usuario);
    }

    public function testGetIdRetornaValorCorreto(): void
    {
        $this->assertSame(1, $this->usuario->getId());
    }

    public function testGetNomeRetornaValorCorreto(): void
    {
        $this->assertSame('João', $this->usuario->getNome());
    }

    public function testGetEmailRetornaValorCorreto(): void
    {
        $this->assertSame('joao@example.com', $this->usuario->getEmail());
    }

    public function testSalvarRetornaTrue(): void
    {
        ob_start();
        $result = $this->usuario->salvar();
        ob_end_clean();

        $this->assertTrue($result);
    }

    public function testEditarRetornaTrue(): void
    {
        ob_start();
        $result = $this->usuario->editar();
        ob_end_clean();

        $this->assertTrue($result);
    }

    public function testExcluirRetornaTrue(): void
    {
        ob_start();
        $result = $this->usuario->excluir();
        ob_end_clean();

        $this->assertTrue($result);
    }

    public function testCadastrarOuAtualizarRetornaInstanciaDoProprioObjeto(): void
    {
        ob_start();
        $result = $this->usuario->cadastrarOuAtualizar();
        ob_end_clean();

        $this->assertSame($this->usuario, $result);
    }


    public function testBuscarPorIdRetornaInstanciaDoProprioObjeto(): void
    {
        ob_start();
        $result = $this->usuario->buscarPorId();
        ob_end_clean();

        $this->assertSame($this->usuario, $result);
    }


    public function testRegistrarLogDelegaParaLoggerInjetado(): void
    {
        $logger = $this->createMock(LogInterface::class);
        $logger->expects($this->once())
            ->method('registrarLog')
            ->with($this->stringContains('João'));

        ob_start();
        $usuario = new Usuario(1, 'João', 'joao@example.com', $logger, $this->notificacao);
        ob_end_clean();

        $usuario->registrarLog('ação executada.');
    }

    public function testEnviarNotificacaoDelegaParaNotificacaoInjetada(): void
    {
        $notificacao = $this->createMock(NotificacaoInterface::class);
        $notificacao->expects($this->once())
            ->method('enviarNotificacao')
            ->with($this->isInstanceOf(Usuario::class), 'Bem-vindo!')
            ->willReturn(true);

        ob_start();
        $usuario = new Usuario(1, 'João', 'joao@example.com', $this->logger, $notificacao);
        ob_end_clean();

        $result = $usuario->enviarNotificacao('Bem-vindo!');

        $this->assertTrue($result);
    }
}
