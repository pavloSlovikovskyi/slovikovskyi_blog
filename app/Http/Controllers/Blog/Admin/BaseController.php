<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Http\Controllers\Controller; // або інший базовий контролер, від якого він успадковується
use Illuminate\Foundation\Bus\DispatchesJobs; // <--- ДОДАЙТЕ ЦЕЙ РЯДОК

class BaseController extends Controller // або ваш власний базовий клас
{
    use DispatchesJobs; // <--- ДОДАЙТЕ ЦЕЙ РЯДОК

    public function __construct()
    {
        // Базові налаштування або конструктор
    }
}
