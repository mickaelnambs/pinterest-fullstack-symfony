<?php

namespace App\Controller\Back;

use App\Service\StatsService;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class AdminDashboardController.
 * 
 * @IsGranted("ROLE_ADMIN")
 * 
 * @author Mickael Nambinintsoa <mickael.nambinintsoa07081999@gmail.com>
 */
class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard_index")
     */
    public function index(StatsService $statsService)
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $statsService->getStats()
        ]);
    }
}
