<?php

namespace App\Controller;

use App\Service\PrimeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class PrimeController extends AbstractController
{
    private $primeService;

    public function __construct(PrimeService $primeService)
    {
        $this->primeService = $primeService;
    }

    #[Route('/prime', name: 'app_prime')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PrimeController.php',
        ]);
    }

    #[Route('/prime/{number}', name: 'prime_check', methods: ['GET'])]
    public function checkPrime($number): JsonResponse
    {
        $isPrime = $this->primeService->isPrime($number);
        return $this->json([
            'number' => $number,
            'isPrime' => $isPrime,
        ]);
    }
}
