<?php
// src/Controller/ApplicationController.php

namespace App\Controller;

use App\Entity\Application;
use App\Form\ApplicationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractController
{
    #[Route('/application/create', name: 'app_create_application')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $application = new Application();

        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($application);
            $em->flush();

            $this->addFlash('success', 'Application created successfully!');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('application/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
