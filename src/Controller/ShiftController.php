<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\OrdioApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/shifts')]
class ShiftController extends AbstractController
{
    public function __construct(
        private readonly OrdioApiService $ordioApiService,
    ) {
    }

    #[Route('/december', name: 'shifts_december', methods: ['GET'])]
    public function getDecemberShifts(Request $request): JsonResponse
    {
        $year = $request->query->getInt('year', (int) date('Y'));

        try {
            $shifts = $this->ordioApiService->getShiftsForDecember($year);

            return $this->json([
                'success' => true,
                'year' => $year,
                'count' => count($shifts),
                'shifts' => $shifts,
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
