<?php

namespace App\Controller;

use App\Entity\Portfolio;
use App\Repository\PortfolioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(PortfolioRepository $portfolioRepository): Response
    {
        // Получаем текущего пользователя через $this->getUser()
        $user = $this->getUser();

        // Получаем портфели для текущего пользователя
        $portfolios = $portfolioRepository->findBy(['user' => $user]);
        $totalBalance = 0;

        // Суммируем балансы всех портфелей
        foreach ($portfolios as $portfolio) {
            $totalBalance += $portfolio->getBalance();
        }

        // Отправляем данные в шаблон
        return $this->render('profile/index.html.twig', [
            'username' => $user->getUsername(),
            'portfolios' => $portfolios,
            'totalBalance' => $totalBalance,
        ]);
    }

    #[Route('/profile/create', name: 'app_create_portfolio', methods: ['POST'])]
    public function createPortfolio(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Получаем текущего пользователя
        $user = $this->getUser();

        // Проверяем, сколько портфелей уже есть у пользователя
        $portfoliosCount = count($user->getPortfolios());

        // Если у пользователя уже есть 5 портфелей, показываем ошибку
        if ($portfoliosCount >= 5) {
            $this->addFlash('error', 'You can only have up to 5 portfolios.');
            return $this->redirectToRoute('app_profile');
        }

        // Создаем новый портфель
        $portfolio = new Portfolio();
        $portfolio->setUser($user);
        $portfolio->setBalance(0); // Начальный баланс

        // Сохраняем портфель в базе данных
        $entityManager->persist($portfolio);
        $entityManager->flush();

        // Показываем сообщение об успехе
        $this->addFlash('success', 'New portfolio created!');
        return $this->redirectToRoute('app_profile');
    }
}
