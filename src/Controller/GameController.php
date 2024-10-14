<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\Game;

class GameController extends Controller
{
    public function index()
    {
        // Vérifier si la session est démarrée (si nécessaire)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id_createur'])) {
            // Rediriger vers la page de connexion
            HTTP::redirect('/createurs/login');
        }
        // Récupérer l'identifiant du créateur depuis la session
        $idCreateur = $_SESSION['id_createur'] ?? null;

        // Passer l'`idCreateur` à la vue
        $this->display('game/index.html.twig', compact('idCreateur'));
    }
}
