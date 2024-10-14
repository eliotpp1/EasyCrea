<?php

declare(strict_types=1);
/*
-------------------------------------------------------------------------------
les routes
-------------------------------------------------------------------------------
 */

return [

    // accueil et affichage pour les avatars
    ['GET', '/avatars', 'avatar@index'],
    ['GET', '/', 'avatar@index'],

    // afficher le formulaire d'ajout d'un nouvel avatar
    ['GET', '/avatars/ajouter', 'avatar@create'],
    // enregistrer les données soumises d'un nouvel avatar
    ['POST', '/avatars/ajouter', 'avatar@create'],

    // afficher le formulaire d'édition un avatar existant
    // à compléter ...

    // enregistrer les modifications sur un avatar existant
    // à compléter ...

    // effacer un avatar
    ['GET', '/avatars/effacer/{id:\d+}', 'avatar@delete'],

];
