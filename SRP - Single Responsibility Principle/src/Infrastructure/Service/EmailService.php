<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Infrastructure\Service;

use SrpSingleresponsibilityprinciple\Domain\Contract\EmailServiceInterface;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Email;

class EmailService implements EmailServiceInterface
{
    public function send(Email $email): bool
    {
        echo "Email enviado para: " . $email->getEmail() . "\nAssunto: " . $email->getAssunto() . "\nMensagem: " . $email->getMensagem();

        return true;
    }
}
