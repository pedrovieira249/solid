<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Domain\Entity;

use IspInterfacesegregationprinciple\Infrastructure\Database\DBConnection;
use IspInterfacesegregationprinciple\Domain\Contract\UsuarioWriteInterface;
use IspInterfacesegregationprinciple\Domain\Contract\UsuarioReadInterface;
use IspInterfacesegregationprinciple\Domain\Contract\LogInterface;
use IspInterfacesegregationprinciple\Domain\Contract\NotificacaoInterface;
use IspInterfacesegregationprinciple\Domain\Contract\NotificacaoRecipientInterface;

class Usuario extends DBConnection implements UsuarioWriteInterface, UsuarioReadInterface, NotificacaoRecipientInterface
{
    public function __construct(
        private int $id,
        private string $nome,
        private string $email,
        private LogInterface $logger,
        private NotificacaoInterface $notificacao
    ){
        parent::__construct();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function salvar(): bool
    {
        echo "Usuário '{$this->nome}' com email '{$this->email}' salvo com sucesso.\n";
        return true;
    }

    public function editar(): bool
    {
        echo "Usuário '{$this->nome}' com email '{$this->email}' editado com sucesso.\n";
        return true;
    }

    public function excluir(): bool
    {
        echo "Usuário '{$this->nome}' com email '{$this->email}' excluído com sucesso.\n";
        return true;
    }

    public function buscarPorId(): static|null
    {
        echo "Usuário '{$this->nome}' com email '{$this->email}' encontrado por ID.\n";
        return $this;
    }

    public function cadastrarOuAtualizar(): static
    {
        echo "Usuário '{$this->nome}' com email '{$this->email}' cadastrado ou atualizado com sucesso.\n";
        return $this;
    }

    public function registrarLog(string $mensagem): void
    {
        $this->logger->registrarLog("Usuário '{$this->nome}': " . $mensagem);
    }

    public function enviarNotificacao(string $mensagem): bool
    {
        return $this->notificacao->enviarNotificacao($this, $mensagem);
    }
}
