<?php

namespace App\Controllers;

use App\Helpers\View;

class AdminController
{
    public function index(): void
    {
        View::render('admin/dashboard', [
            'title' => 'Admin Panel - Puno at Halaman AI'
        ]);
    }
}
