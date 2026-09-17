<?php

namespace App\Controllers;

use App\Helpers\View;

class AuthController
{
    public function loginForm(): void
    {
        View::render('auth/login', [
            'title' => 'Mag-login - Puno at Halaman AI'
        ]);
    }
}
