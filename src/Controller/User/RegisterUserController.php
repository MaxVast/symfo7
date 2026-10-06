<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\UserType;
use App\Repository\UserRepository;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
#[Route(path: '/inscription', name: 'inscription_user', methods: ['GET', 'POST'])]
class RegisterUserController
{
    public function __invoke(Environment $twig, Request $request, UserRepository $userRepository,
                             UserPasswordHasherInterface $passwordHasher, FormFactoryInterface $formFactory): Response
    {
        $user = new User();
        $form = $formFactory->create(UserType::class, $user);

        $form->handleRequest($request);
        try {
            if($form->isSubmitted() && $form->isValid()){
                $hashedPassword = $passwordHasher->hashPassword($user, $form->get('password')->get('first')->getData());



                $user = $form->getData();
                $user->setPassword($hashedPassword);
                $userRepository->persistAndSave($user);
            }
        } catch (LogicException $e){

        }

        return new Response($twig->render('user/register.html.twig',[
            'form' => $form->createView()
        ]), Response::HTTP_OK);
    }
}
