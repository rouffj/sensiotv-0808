<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response
    {
        return new Response('logout response');
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheck(): Response
    {
        return new Response('login Check response');
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(RegisterType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('terms')->getData()) {
            // The form has no error and the user has clicked on submit button.
            /** @var User $user */
            $user = $form->getData();
            $encodedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            dump($user);
            $this->addFlash('success', 'Your account has been successfully created');

            return $this->redirectToRoute('home');
        }

        return $this->render('security/register.html.twig', [
            'register_form' => $form->createView()
        ]);
    }
}
