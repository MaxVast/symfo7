<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

#[Route('/login', name: 'app_login')]
class LoginController
{
    public function __invoke(AuthenticationUtils $authenticationUtils,
                             Environment $twig): Response
    {
        return new Response($twig->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]), Response::HTTP_OK);
    }
}
