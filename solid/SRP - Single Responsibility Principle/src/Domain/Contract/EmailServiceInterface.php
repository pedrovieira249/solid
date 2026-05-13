<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Contract;

use SrpSingleresponsibilityprinciple\Domain\ValueObject\Email;

interface EmailServiceInterface
{
    public function send(Email $email): bool;
}
