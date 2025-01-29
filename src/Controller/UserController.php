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

    /**
     * @Route("/admin/grant/{id}", name="admin_grant")
     */
    public function grantAdmin(User $user): Response
    {
        // Проверка, что текущий пользователь имеет права супер админа
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Only super admins can grant admin rights.');
        }

        $user->setRoles(['ROLE_ADMIN']);
        $this->entityManager->flush();
        return new Response('Admin rights granted');
    }

    /**
     * @Route("/admin/revoke/{id}", name="admin_revoke")
     */
    public function revokeAdmin(User $user): Response
    {
        // Проверка, что текущий пользователь имеет права супер админа
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Only super admins can revoke admin rights.');
        }

        $user->setRoles([]);
        $this->entityManager->flush();
        return new Response('Admin rights revoked');
    }
}