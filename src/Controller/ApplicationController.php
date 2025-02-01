<?php
// src/Controller/ApplicationController.php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Portfolio;
use App\Entity\Stock;
use App\Form\ApplicationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ApplicationRepository;

class ApplicationController extends AbstractController
{
    #[Route('/glass/{stock_id}', name: 'app_view_stock_applications', methods: ['GET'])]
    public function viewStockApplications(int $stock_id, EntityManagerInterface $em): Response
    {
        // Находим все заявки для данного stock_id
        $stock = $em->getRepository(Stock::class)->find($stock_id);

        if (!$stock) {
            $this->addFlash('error', 'Stock not found!');
            return $this->redirectToRoute('app_profile');
        }

        // Получаем все заявки для указанного stock
        $applications = $em->getRepository(Application::class)->findBy(['stock' => $stock]);

        return $this->render('application/view_stock_applications.html.twig', [
            'stock' => $stock,
            'applications' => $applications,
        ]);
    }

    #[Route('/application/create', name: 'app_create_application', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Получаем данные portfolio и stock из формы
            $portfolio = $form->get('portfolio')->getData();
            $stock = $form->get('stock')->getData();

            // Валидация: проверка существования portfolio и stock
            if (!$portfolio || !$stock) {
                $this->addFlash('error', 'Invalid portfolio or stock.');
                return $this->redirectToRoute('app_create_application');
            }

            // Если все валидно, сохраняем заявку в базе данных
            $application->setPortfolio($portfolio);
            $application->setStock($stock);

            $em->persist($application);
            $em->flush();

            $this->addFlash('success', 'Application created successfully!');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('application/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Обновление заявки
    #[Route('/application/update/{application_id}', name: 'application_update', methods: ['GET', 'PUT'])]
    public function update(Request $request, int $application_id, EntityManagerInterface $em): Response
    {
        $application = $em->getRepository(Application::class)->find($application_id);

        if (!$application) {
            $this->addFlash('error', 'Application not found!');
            return $this->redirectToRoute('app_profile');
        }

        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Application updated successfully!');
            return $this->redirectToRoute('app_profile');
        }

        // Передаем переменную application в шаблон
        return $this->render('application/edit.html.twig', [
            'form' => $form->createView(),
            'application' => $application,  // Передаем объект 'application'
        ]);
    }

    // Удаление заявки
    #[Route('/application/delete/{id}', name: 'application_delete', methods: ['GET', 'POST'])]
    public function delete(int $id, EntityManagerInterface $em, Request $request): Response
    {
        // Находим заявку по ID
        $application = $em->getRepository(Application::class)->find($id);

        if (!$application) {
            $this->addFlash('error', 'Application not found!');
            return $this->redirectToRoute('app_profile');
        }

        // Если запрос POST, то удаляем заявку
        if ($request->isMethod('POST')) {
            $em->remove($application);
            $em->flush();

            $this->addFlash('success', 'Application deleted successfully!');
            return $this->redirectToRoute('app_profile');
        }

        // Если GET запрос, показываем страницу подтверждения удаления
        return $this->render('application/delete.html.twig', [
            'application' => $application,  // передаем саму заявку в шаблон
        ]);
    }
}