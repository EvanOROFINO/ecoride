<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'pageTitle'       => 'Accueil',
            'pageDescription' => 'EcoRide, la plateforme de covoiturage écologique. Voyagez ensemble, polluez moins.',
        ]);
    }

    public function mentionsLegales(): void
    {
        $this->view('home/mentions-legales', [
            'pageTitle' => 'Mentions légales',
        ]);
    }
}
