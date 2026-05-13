<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Infrastructure\Database;

class DBConnection
{
    public function __construct(
        private string $host = 'localhost:3307',
        private string $username = 'root',
        private string $password = '123456',
        private string $database = 'test'
    ) {
        $this->connect();
    }

    private function connect(): void
    {
        if (empty($this->host) || empty($this->username) || empty($this->password) || empty($this->database)) {
            throw new \InvalidArgumentException("Nenhum dado de conexão pode estar vazio.");
        } else {
            echo "Conectado no banco '{$this->database}' em '{$this->host}' com o usuário '{$this->username}'.\n";
        }
    }
}
