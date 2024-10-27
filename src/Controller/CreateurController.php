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
            return;
        }

        // 1. Récupérer et valider les données
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $ddn = trim($_POST['ddn']);
        $genre = trim($_POST['genre']);

        if (empty($name) || empty($email) || empty($password) || empty($ddn) || empty($genre)) {
            $this->display('createurs/register.html.twig', ['error' => 'Tous les champs sont requis']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->display('createurs/register.html.twig', ['error' => 'Adresse email invalide']);
            return;
        }

        if (strlen($password) < 8) {
            $this->display('createurs/register.html.twig', ['error' => 'Le mot de passe doit contenir au moins 8 caractères']);
            return;
        }


        // 2. Vérifier si l'email existe déjà
        $existingUser = Createur::getInstance()->findByEmail($email);
        if ($existingUser) {
            // Message d'erreur générique pour éviter les attaques par énumération
            $this->display('createurs/register.html.twig', ['error' => 'Impossible de s\'inscrire avec cet email  ']);
            return;
        }

        // 3. Créer le créateur en utilisant un algorithme de hachage sécurisé
        Createur::getInstance()->create([
            'nom_createur' => $name,
            'ad_email_createur' => $email,
            'mdp_createur' => password_hash($password, PASSWORD_BCRYPT), // Utilisation de Argon2 pour plus de sécurité
            'ddn' => $ddn,
            'genre' => $genre,
        ]);

        // 4. Rediriger vers la page de connexion
        HTTP::redirect('/createurs/login');
    }


    public function login()
    {
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

            session_start([
                'cookie_path' => '/',
                'cookie_lifetime' => 0,
                'cookie_secure' => true,
                'cookie_httponly' => true,
                'cookie_samesite' => 'strict',
            ]);

            // 3. Si le créateur est trouvé, vérifier le mot de passe
            if ($createur && password_verify($password, $createur['mdp_createur'])) {
                // 4. Stocker l'identifiant du créateur dans la session
                $_SESSION['id_createur'] = $createur['id_createur'];

                // 5. Rediriger vers la page d'accueil
                HTTP::redirect('/game');
            } else {
                // 6. Sinon, afficher un message d'erreur
                $this->display('createurs/login.html.twig', ['error' => 'Identifiant ou mot de passe incorrect']);
            }
        }
    }





    public function logout()
    {
        session_start([
            'cookie_path' => '/',
            'cookie_lifetime' => 0,
            'cookie_secure' => true,
            'cookie_httponly' => true,
            'cookie_samesite' => 'strict',
        ]);
        session_destroy();
        HTTP::redirect('/');
    }
}
