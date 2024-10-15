<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\Deck;
use App\Model\Carte;
use App\Model\CarteAleatoire;

class GameController extends Controller
{
    public function index()
    {
        // Vérifier si la session est démarrée (si nécessaire)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['id_createur'])) {
            // Rediriger vers la page de connexion
            HTTP::redirect('/createurs/login');
        }

        // Récupérer l'id du créateur
        $idCreateur = $_SESSION['id_createur'];

        // Vérifier si un deck est en live
        $deck = Deck::getInstance()->getLiveDeck(); // Méthode à créer pour récupérer le deck live

        if ($deck) {
            // Vérifier si une carte aléatoire est déjà associée à ce deck et ce créateur
            $carteAleatoire = CarteAleatoire::getInstance()->getCardForDeckAndCreator($deck['id_deck'], $idCreateur);

            if (!$carteAleatoire) {
                // Si aucune carte aléatoire, choisir une carte aléatoire
                $carte = Carte::getInstance()->getRandomCard($deck['id_deck']); // Méthode à créer pour récupérer une carte aléatoire

                // Si une carte est trouvée, l'associer à ce deck et ce créateur
                if ($carte) {
                    CarteAleatoire::getInstance()->addCardForDeckAndCreator($deck['id_deck'], $idCreateur, $carte['id_carte']);
                    $carteAleatoire = $carte;
                }
            } else {
                // Récupérer les détails de la carte associée
                $carteAleatoire = Carte::getInstance()->getCardById($carteAleatoire['id_carte']);
            }

            // Récupérer le nombre total de cartes dans le deck
            $totalCartes = Deck::getInstance()->getTotalCardsInDeck($deck['id_deck']); // Méthode à créer pour récupérer le total des cartes


            if (!$carteAleatoire) {
                // Si aucune carte n'est trouvée dans le deck
                $texteCarte = "Aucune carte disponible.";
                $valeursChoix1 = '';
                $valeursChoix2 = '';
            } else {
                // Récupérer les détails de la carte
                $la_carte = Carte::getInstance()->getCardById($carteAleatoire['id_carte']);
                $texteCarte = $la_carte['texte_carte'];
                $valeursChoix1 = $la_carte['valeurs_choix1'];
                $valeursChoix2 = $la_carte['valeurs_choix2'];
            }
        } else {
            // Si aucun deck live
            $texteCarte = "Aucune fabrication de deck en cours.";
            $valeursChoix1 = '';
            $valeursChoix2 = '';
            $totalCartes = 0; // Pas de cartes si pas de deck
        }
        // dd($idCreateur);

        // Passer les données à la vue
        $this->display('game/index.html.twig', compact('idCreateur', 'texteCarte', 'valeursChoix1', 'valeursChoix2', 'totalCartes'));
    }
}
