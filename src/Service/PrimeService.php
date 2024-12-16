<?php
// src/Service/PrimeService.php

namespace App\Service;

class PrimeService
{
    public function isPrime($number): bool
    {
        if ($number <= 1) {
            return false;
        }
        for ($i = 2; $i <= sqrt($number); $i++) {
            if ($number % $i == 0) {
                return false;
            }
        }
        return true;
    }
}

