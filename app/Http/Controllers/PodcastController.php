<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use App\Http\Requests\StorePodcastRequest;
use App\Http\Requests\UpdatePodcastRequest;
use App\Services\CloudinaryService;

class PodcastController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $podcasts = Podcast::with('user', 'episodes')->get();
        return response()->json($podcasts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePodcastRequest $request)
    {
        $user = $request->user();
        
        // Vérifier que l'utilisateur est animateur ou admin
        if (!in_array($user->role, ['animateur', 'admin'])) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Upload de l'image vers Cloudinary
        $imageUrl = $this->cloudinaryService->uploadImage($request->file('image'));

        $podcast = Podcast::create([
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'image' => $imageUrl,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Podcast créé avec succès',
            'podcast' => $podcast
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $podcast = Podcast::with('user', 'episodes')->findOrFail($id);
        return response()->json($podcast);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePodcastRequest $request, $id)
    {
        $podcast = Podcast::findOrFail($id);
        $user = $request->user();

        // Vérifier que l'utilisateur est le propriétaire ou admin
        if ($podcast->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $data = $request->only(['title', 'category', 'description']);

        // Si une nouvelle image est uploadée
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image de Cloudinary
            $this->cloudinaryService->deleteFile($podcast->image);
            
            // Upload la nouvelle image
            $data['image'] = $this->cloudinaryService->uploadImage($request->file('image'));
        }

        $podcast->update($data);

        return response()->json([
            'message' => 'Podcast mis à jour avec succès',
            'podcast' => $podcast
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $podcast = Podcast::findOrFail($id);
        $user = request()->user();

        // Vérifier que l'utilisateur est le propriétaire ou admin
        if ($podcast->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Supprimer l'image de Cloudinary
        $this->cloudinaryService->deleteFile($podcast->image);

        $podcast->delete();

        return response()->json(['message' => 'Podcast supprimé avec succès']);
    }
}
