<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\Admin;

class AdminController extends Controller

{
    public function login()
    {
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

            session_start([
                'cookie_path' => '/',
                'cookie_lifetime' => 0,
                'cookie_secure' => true,
                'cookie_httponly' => true,
                'cookie_samesite' => 'strict',
            ]);

            // 3. Si le créateur est trouvé, vérifier le mot de passe
            if ($admin && password_verify($password, $admin['mdp_createur'])) {
                // 4. Stocker l'identifiant du créateur dans la session
                $_SESSION['id_createur'] = $admin['id_createur'];

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
