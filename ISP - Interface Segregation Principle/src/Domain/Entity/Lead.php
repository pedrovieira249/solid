<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Domain\Entity;

use IspInterfacesegregationprinciple\Domain\Contract\LeadInterface;
use IspInterfacesegregationprinciple\Infrastructure\Database\DBConnection;
use IspInterfacesegregationprinciple\Domain\Contract\LogInterface;

class Lead extends DBConnection implements LeadInterface
{
    public function __construct(
        private string $nome,
        private string $email,
        private LogInterface $logger
    ) {
        parent::__construct();
    }

    public function gerar(): bool
    {
        echo "Lead '{$this->nome}' com email '{$this->email}' gerado com sucesso.\n";
        $this->logger->registrarLog("Lead '{$this->nome}' gerado.");
        return true;
    }
}
