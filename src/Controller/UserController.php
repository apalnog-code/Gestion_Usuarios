<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserNewType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{

    #[Route(path: '/users', name: 'users_list')]
    public function listarUsuarios(UserRepository $userRepository): Response
    {
        $users = $userRepository->usserList();
        return $this->render('users/users_list.html.twig',[
            'users' => $users
        ]);
    }

    #[Route(path: '/user/create', name: 'user_create')]
    public function createUser(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $user->setIsActive(false);
        $user->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Madrid')));
        $form = $this->createForm(UserNewType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $userRepository->add($user);
            $userRepository->save();
            $this->addFlash('success','The user has been created succesfully');
            return $this->redirectToRoute('users_list');
        } else {
            $this->addFlash('error','The user can´t be created');
        }

        return $this->render('users/users_create.html.twig',[
            'user' => $user,
            'form' => $form
        ]);
    }

    #[Route('/user/edit/{id}', name: 'user_edit')]
    public function editUser(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $userRepository->add($user);
            $userRepository->save();
            $this->addFlash('success','The user was modified successfully');
            return $this->redirectToRoute('users_list');
        } else {
            $this->addFlash('error','The user can´t be created');
        }

        return $this->render('users/users_create.html.twig',[
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function deleteUser(User $user, UserRepository $userRepository): Response {

            $userRepository->remove($user);
            $userRepository->save();
            return $this->redirectToRoute('users_list');
    }


    #[Route('/user/activar/{id}', name: 'user_active')]
    public function activeUser(User $user, UserRepository $userRepository): Response {
        $user->isActive() == 0 ? $user->setIsActive(1) : $user->setIsActive(0);
        $userRepository->add($user);
        $userRepository->save();
        $this->addFlash('success','The user has been modified succesfully');
        return $this->redirectToRoute('users_list');
    }


}
