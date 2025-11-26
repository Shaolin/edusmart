<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class LogoController extends Controller
{
    public function show($filename)
    {
        $path = storage_path('app/public/' . $filename);
    
        if (!file_exists($path)) {
            abort(404);
        }
    
        return response()->file($path);
    }
    
}
