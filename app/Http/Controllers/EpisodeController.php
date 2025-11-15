<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Podcast;
use App\Http\Requests\StoreEpisodeRequest;
use App\Http\Requests\UpdateEpisodeRequest;
use App\Services\CloudinaryService;

class EpisodeController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($podcast_id)
    {
        $episodes = Episode::where('podcast_id', $podcast_id)->with('podcast')->get();
        return response()->json($episodes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEpisodeRequest $request, $podcast_id)
    {
        $podcast = Podcast::findOrFail($podcast_id);
        $user = $request->user();

        // Vérifier que l'utilisateur est le propriétaire du podcast ou admin
        if ($podcast->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Upload de l'audio vers Cloudinary
        $audioUrl = $this->cloudinaryService->uploadAudio($request->file('audio'));

        $episode = Episode::create([
            'title' => $request->title,
            'description' => $request->description,
            'audio' => $audioUrl,
            'podcast_id' => $podcast_id,
        ]);

        return response()->json([
            'message' => 'Épisode créé avec succès',
            'episode' => $episode
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $episode = Episode::with('podcast')->findOrFail($id);
        return response()->json($episode);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEpisodeRequest $request, $id)
    {
        $episode = Episode::findOrFail($id);
        $podcast = $episode->podcast;
        $user = $request->user();

        // Vérifier que l'utilisateur est le propriétaire du podcast ou admin
        if ($podcast->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $data = $request->only(['title', 'description']);

        // Si un nouveau fichier audio est uploadé
        if ($request->hasFile('audio')) {
            // Supprimer l'ancien audio de Cloudinary
            $this->cloudinaryService->deleteFile($episode->audio);
            
            // Upload le nouveau audio
            $data['audio'] = $this->cloudinaryService->uploadAudio($request->file('audio'));
        }

        $episode->update($data);

        return response()->json([
            'message' => 'Épisode mis à jour avec succès',
            'episode' => $episode
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $episode = Episode::findOrFail($id);
        $podcast = $episode->podcast;
        $user = request()->user();

        // Vérifier que l'utilisateur est le propriétaire du podcast ou admin
        if ($podcast->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Supprimer l'audio de Cloudinary
        $this->cloudinaryService->deleteFile($episode->audio);

        $episode->delete();

        return response()->json(['message' => 'Épisode supprimé avec succès']);
    }
}
