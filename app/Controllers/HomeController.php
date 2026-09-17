<?php

namespace App\Controllers;

use App\Helpers\View;

class HomeController
{
    public function index(): void
    {
        View::render('home', [
            'title' => 'Puno at Halaman AI - Philippine Plant & Tree Identifier'
        ]);
    }
}
