<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/grant/{id}', name: 'admin_grant', methods: ['GET', 'POST'])]
    public function grantAdmin(int $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Only super admins can grant admin rights.');
        }

        if (!in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $user->setRoles([...$user->getRoles(), 'ROLE_ADMIN']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return new Response('Admin rights granted');
    }

    #[Route('/admin/revoke/{id}', name: 'admin_revoke', methods: ['GET', 'POST'])]
    public function revokeAdmin(int $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Only super admins can revoke admin rights.');
        }

        $user->setRoles(array_values(array_diff($user->getRoles(), ['ROLE_ADMIN'])));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response('Admin rights revoked');
    }
}
