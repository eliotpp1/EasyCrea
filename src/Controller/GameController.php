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
        $deck = Deck::getInstance()->getLiveDeck(); // Méthode pour récupérer le deck live


        // Vérifier si le deck trouvé en live n'est pas passé de date
        if ($deck && strtotime($deck['date_fin_deck']) < time()) {
            // Désactiver le deck
            Deck::getInstance()->disableDeck($deck['id_deck']);
            $deck = null;
        }

        if ($deck) {
            // Vérifier si une carte aléatoire est déjà associée à ce deck et ce créateur
            $carteAleatoire = CarteAleatoire::getInstance()->getCardForDeckAndCreator($deck['id_deck'], $idCreateur);

            if (!$carteAleatoire) {
                // Choisir une carte aléatoire
                $carte = Carte::getInstance()->getRandomCard($deck['id_deck']); // Méthode pour récupérer une carte aléatoire

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
            $totalCartes = Deck::getInstance()->getTotalCardsInDeck($deck['id_deck']); // Méthode pour récupérer le total des cartes

            if (!$carteAleatoire) {
                // Si aucune carte n'est trouvée dans le deck
                $texteCarte = "Aucune carte disponible.";
                $valeursChoix1 = '';
                $valeursChoix2 = '';
            } else {
                // Récupérer les détails de la carte
                $la_carte = Carte::getInstance()->getCardById($carteAleatoire['id_carte']);
                $texteCarte = $la_carte['texte_carte'];
                $valeursChoix1 = explode(',', $la_carte['valeurs_choix1']);
                $valeursChoix2 = explode(',', $la_carte['valeurs_choix2']);
            }
        } else {
            HTTP::redirect('/noDecks');
        }

        // Initialiser un message d'erreur
        $error = '';

        // Vérifiez si la requête est POST pour ajouter une carte
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si le créateur a déjà créé une carte
            $cardAlreadyCreated = Carte::getInstance()->getIfCreatorHasCreatedCard($idCreateur);
            if ($cardAlreadyCreated) {
                // Ne rien faire et afficher un message d'erreur
                $error = "Vous avez déjà créé une carte.";
            } else {
                // Récupérer les données du formulaire
                $texteCarte = htmlspecialchars(trim($_POST['texte_carte']));
                $valeursChoix1 = htmlspecialchars(trim($_POST['valeurs_choix1'] . ',' . $_POST['valeurs_choix1bis']));
                $valeursChoix2 = htmlspecialchars(trim($_POST['valeurs_choix2bis'] . ',' . $_POST['valeurs_choix2']));

                // Vérifier le nombre de cartes dans le deck
                $carteDansLeDeck = Carte::getInstance()->getNumberOfCardsInDeck($deck['id_deck']); // Méthode pour récupérer le nombre de cartes dans le deck

                if ($carteDansLeDeck < $totalCartes) {
                    $ordreSoumission = $carteDansLeDeck + 1;

                    // Créer la carte
                    Carte::getInstance()->create([
                        'date_soumission' => (new \DateTime())->format('Y-m-d'), // Format de date adapté
                        'ordre_soumission' => $ordreSoumission,
                        'valeurs_choix1' => $valeursChoix1,
                        'texte_carte' => $texteCarte,
                        'valeurs_choix2' => $valeursChoix2,
                        'id_deck' => $deck['id_deck'],
                        'id_createur' => $idCreateur,
                    ]);

                    // Rediriger vers la même page ou une autre page après l'ajout
                    HTTP::redirect('/game');
                } else {
                    // Afficher un message d'erreur si le deck est complet
                    Deck::getInstance()->disableDeck($deck['id_deck']);
                    $error = "Le deck est complet. Vous ne pouvez pas ajouter plus de cartes.";
                }
            }
        }
        // dd($valeursChoix1, $valeursChoix2);
        // Passer les données à la vue
        $this->display('game/index.html.twig', compact('idCreateur', 'texteCarte', 'valeursChoix1', 'valeursChoix2', 'totalCartes', 'la_carte', 'deck', 'error'));
    }



    public function noDecks()
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
        // Afficher la page d'erreur
        $this->display('game/noDecks.html.twig');
    }
}
