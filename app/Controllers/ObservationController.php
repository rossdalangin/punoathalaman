<?php

namespace App\Controllers;

use App\Helpers\View;

class ObservationController
{
    public function index(): void
    {
        View::render('observations/index', [
            'title' => 'Field Observations & Documentation - Puno at Halaman AI'
        ]);
    }
}
