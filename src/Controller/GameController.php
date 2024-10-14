<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\Game;

class GameController extends Controller
{

    public function index()
    {
        $this->display('game/index.html.twig');
    }
}
