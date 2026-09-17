<?php

namespace App\Controllers;

use App\Helpers\View;

class EducationalController
{
    public function index(): void
    {
        View::render('educational/index', [
            'title' => 'Edukasyon at Pag-aaral (Learn Plant ID) - Puno at Halaman AI'
        ]);
    }
}
