<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\Admin;
use App\Model\Carte;
use App\Model\Deck;

class AdminController extends Controller
{
    public function register()
    {
        if ($this->isGetMethod()) {
            $this->display('admin/register.html.twig');
        } else {
            // 1. Vérifier les données soumises
            // 2. Exécuter la requête d'insertion
            Admin::getInstance()->create([
                'ad_email_admin' => trim($_POST['email']),
                'mdp_admin' => trim(password_hash($_POST['password'], PASSWORD_BCRYPT)),
            ]);

            // 3. Rediriger vers la page de connexion
            HTTP::redirect('/admin/login');
        }
    }

    public function login()
    {
        // Vérifie si la méthode de la requête est GET
        if ($this->isGetMethod()) {
            $this->display('admin/login.html.twig');
        } else {
            // 1. Vérifier les données soumises
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            // 2. Exécuter la requête de recherche
            $admin = Admin::getInstance()->findOneBy([
                'ad_email_admin' => $email
            ]);

            // Démarrer la session (à faire en début de script, normalement)
            if (session_status() === PHP_SESSION_NONE) {
                session_start([
                    'cookie_path' => '/',
                    'cookie_lifetime' => 0,
                    'cookie_secure' => true,
                    'cookie_httponly' => true,
                    'cookie_samesite' => 'Strict', // 'Strict' pour le paramètre SameSite
                ]);
            }

            // Debugging : affiche les informations de l'admin et la session


            // 3. Si l'administrateur est trouvé, vérifier le mot de passe
            if ($admin && password_verify($password, $admin['mdp_admin'])) {
                // 4. Stocker l'identifiant de l'administrateur dans la session
                $_SESSION['id_administrateur'] = $admin['id_administrateur']; // Correction de 'id_adminstrateur' à 'id_admin'

                // 5. Rediriger vers la page d'accueil
                HTTP::redirect('/admin/dashboard');
            } else {
                // 6. Sinon, afficher un message d'erreur
                $this->display('admin/login.html.twig', ['error' => 'Identifiant ou mot de passe incorrect']);
            }
        }
    }


    public function createDeck()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifiez que l'administrateur est connecté
        if (!isset($_SESSION['id_administrateur'])) {
            HTTP::redirect('/admin/login');
        }

        if ($this->isGetMethod()) {
            $this->display('admin/createDeck.html.twig');
        } else {

            // Récupérer les données du formulaire
            $titreDeck = trim($_POST['titre_deck']);
            $dateDebutDeck = trim($_POST['date_debut_deck']);
            $dateFinDeck = trim($_POST['date_fin_deck']);
            $nbCarte = (int)trim($_POST['nb_carte']); // Convertir en entie


            // Vérifier si les choix sont bien définis
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Gérer l'erreur de JSON ici
                $this->display('admin/createDeck.html.twig', ['error' => 'Valeurs de choix invalides.']);
                return;
            }

            // 1. Créer un nouveau deck
            $deckId = Deck::getInstance()->create([
                'titre_deck' => $titreDeck,
                'date_debut_deck' => $dateDebutDeck,
                'date_fin_deck' => $dateFinDeck,
                'nb_cartes' => $nbCarte,
            ]);

            $this->display('admin/createFirstCard.html.twig', compact('deckId'));
        }
    }


    public function createFirstCard()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifiez que l'administrateur est connecté
        if (!isset($_SESSION['id_administrateur'])) {
            HTTP::redirect('/admin/login');
        }

        if ($this->isGetMethod()) {
            $this->display('admin/createFirstCard.html.twig');
        } else {
            // Récupérer les données du formulaire
            $texteCarte = trim($_POST['texte_carte']);
            $valeursChoix1 = trim($_POST['valeurs_choix1']);
            $valeursChoix2 = trim($_POST['valeurs_choix2']);
            $valeurs_choix1bis = trim($_POST['valeurs_choix1bis']);
            $valeurs_choix2bis = trim($_POST['valeurs_choix2bis']);
            $deckId = (int)trim($_POST['deckId']);

            $valeur_choixFinal = $valeursChoix1 . ',' . $valeurs_choix1bis;
            $valeur_choixFinal2 = $valeursChoix2 . ',' . $valeurs_choix2bis;


            // Créer la carte
            $carteCreated = Carte::getInstance()->create([
                'date_soumission' => (new \DateTime())->format('Y-m-d'), // Format de date adapté
                'ordre_soumission' => 1,
                'valeurs_choix1' => $valeur_choixFinal,
                'texte_carte' => $texteCarte,
                'valeurs_choix2' => $valeur_choixFinal2,
                'id_deck' => $deckId,
            ]);

            // Vérifier si l'insertion a réussi
            if ($carteCreated) {
                // Rediriger vers une page de succès ou le tableau de bord
                HTTP::redirect('/admin/dashboard');
            } else {
                // Afficher un message d'erreur si l'insertion a échoué
                $this->display('admin/createFirstCard.html.twig', [
                    'error' => 'Une erreur est survenue lors de la création de la carte.'
                ]);
            }
        }
    }

    public function dashboard()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifiez que l'administrateur est connecté
        if (!isset($_SESSION['id_administrateur'])) {
            HTTP::redirect('/admin/login');
        }

        // Récupérer les decks créés par l'administrateur
        $decks = Deck::getInstance()->findAll();

        // Afficher le tableau de bord
        $this->display('admin/dashboard.html.twig', compact('decks'));
    }

    public function delete(int|string $id)
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifiez que l'administrateur est connecté
        if (!isset($_SESSION['id_administrateur'])) {
            HTTP::redirect('/admin/login');
        }

        $id = (int)$id;
        $type = $_GET['type'] ?? null; // Récupérer le paramètre 'type' depuis la requête

        // Vérifier que le type est valide
        if ($type === 'deck') {
            Deck::getInstance()->delete($id);
        } elseif ($type === 'carte') {
            Carte::getInstance()->delete($id);
        } else {
            // Gérer le cas où le type est invalide
            HTTP::redirect('/admin/dashboard?error=invalid_type');
            return;
        }

        // Rediriger vers le tableau de bord après la suppression
        HTTP::redirect('/admin/dashboard');
    }





    public function showDeck(int|string $id)
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifiez que l'administrateur est connecté
        if (!isset($_SESSION['id_administrateur'])) {
            HTTP::redirect('/admin/login');
        }

        $id = (int)$id;

        // Récupérer les cartes du deck
        $cartes = Carte::getInstance()->findAllBy(['id_deck' => $id]);

        // Afficher les cartes du deck
        $this->display('admin/showDeck.html.twig', compact('cartes'));
    }

    public function logout()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Détruire la session
        session_destroy();

        // Rediriger vers la page de connexion
        HTTP::redirect('/admin/login');
    }
}
