<?php

declare(strict_types=1);

namespace App\Controller\Backend\Admin;

use App\Form\Backend\Admin\ChangePasswordType;
use App\Form\Backend\Admin\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mon-compte/', name: 'backend_security_profile_')]
class ProfileController extends AbstractController
{
    #[Route(name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        Request $request
    ): Response {
        $admin        = $this->getUser();
        $profileForm  = $this->createForm(ProfileType::class, $admin);
        $passwordForm = $this->createForm(ChangePasswordType::class);

        if ($profileForm->handleRequest($request)->isSubmitted() && $profileForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'admin.security.profile.edit.success_flash');

            return $this->redirectToRoute('backend_security_profile_edit');
        }

        if ($passwordForm->handleRequest($request)->isSubmitted() && $passwordForm->isValid()) {
            $data               = $passwordForm->getData();
            $currentPassword    = $data['currentPassword'];
            $newPassword        = $data['plainPassword'];

            if (!$userPasswordHasher->isPasswordValid($admin, $currentPassword)) {
                $this->addFlash('error', 'admin.security.profile.edit.invalid_current_password_flash');

                return $this->redirectToRoute('backend_security_profile_edit');
            }


            $admin->setPassword($userPasswordHasher->hashPassword($admin, $newPassword));
            $entityManager->flush();

            $this->addFlash('success', 'admin.security.profile.edit.password_change_success_flash');

            return $this->redirectToRoute('backend_security_profile_edit');
        }

        return $this->render('backend/admin/profile/edit.html.twig', [
            'profile_form'  => $profileForm,
            'password_form' => $passwordForm,
        ]);
    }
}
