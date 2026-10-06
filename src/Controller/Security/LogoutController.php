<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Component\Routing\Attribute\Route;

#[Route('/logout', name: 'logout')]
class LogoutController
{
    public function __invoke()
    {
    }
}
