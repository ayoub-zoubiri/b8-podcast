<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HostController extends Controller
{
    public function index()
    {
        // Récupérer tous les animateurs
        $hosts = User::where('role', 'animateur')->with('podcasts')->get();
        return response()->json($hosts);
    }

    public function show($id)
    {
        // Récupérer un animateur spécifique avec ses podcasts
        $host = User::where('role', 'animateur')->with('podcasts.episodes')->findOrFail($id);
        return response()->json($host);
    }
}
