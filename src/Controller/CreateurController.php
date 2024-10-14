<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\Createur;

class CreateurController extends Controller

{

    public function register()
    {
        if ($this->isGetMethod()) {
            $this->display('createurs/register.html.twig');
        } else {
            // 1. vérifier les données soumises
            // 2. exécuter la requête d'insertion
            Createur::getInstance()->create([
                'nom_createur' => trim($_POST['name']),
                'ad_email_createur' => trim($_POST['email']),
                'mdp_createur' => trim(password_hash($_POST['password'], PASSWORD_BCRYPT)),
                'ddn' => trim($_POST['ddn']),
                'genre' => trim($_POST['genre']),
            ]);
            // 3. rediriger vers la page de connexion
            HTTP::redirect('/createurs/login');
        }
    }

    public function login()

    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ($this->isGetMethod()) {
            $this->display('createurs/login.html.twig');
        } else {
            // 1. Vérifier les données soumises
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            // 2. Exécuter la requête de recherche
            $createur = Createur::getInstance()->findOneBy([
                'ad_email_createur' => $email
            ]);

            // 3. Si le créateur est trouvé, vérifier le mot de passe
            if ($createur && password_verify($password, $createur['mdp_createur'])) {
                // 4. Stocker l'identifiant du créateur dans la session
                $_SESSION['createur_id'] = $createur['id_createur'];
                $id_createur = $_SESSION['createur_id'];

                // 5. Rediriger vers la page d'accueil
                HTTP::redirect('/game', compact('id_createur'));
            } else {
                // 6. Sinon, afficher un message d'erreur
                $this->display('createurs/login.html.twig', ['error' => 'Identifiant ou mot de passe incorrect']);
            }
        }
    }


    public function logout()
    {
        // 1. effacer l'identifiant de l'avatar de la session
        unset($_SESSION['createur_id']);
        // 2. rediriger vers la page d'accueil
        HTTP::redirect('/');
    }
}
