<?php

declare(strict_types=1);
/*
-------------------------------------------------------------------------------
les routes
-------------------------------------------------------------------------------
 */

return [
    // gèrer l'acceuil
    ['GET', '/', 'createur@login'],

    // gérer la creations de createurs
    ['GET', '/createurs/register', 'createur@register'],
    ['POST', '/createurs/register', 'createur@register'],

    // gérer les connexions de créateurs
    ['GET', '/createurs/login', 'createur@login'],
    ['POST', '/createurs/login', 'createur@login'],

    // gérer les déconnexions de créateurs
    ['GET', '/createurs/logout', 'createur@logout'],

    // gérer les actions quand utilisateur est connecté
    ['GET', '/game', 'game@index'],

    // gérer la déconnexion de l'utilisateur
    ['GET', '/createur/logout', 'createur@logout'],

];
