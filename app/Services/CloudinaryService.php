<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryService
{
    /**
     * Upload une image vers Cloudinary
     */
    public function uploadImage($file)
    {
        try {
            // Augmenter le temps d'exécution pour l'upload
            set_time_limit(120); // 2 minutes
            
            $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'podcasts/images',
                'resource_type' => 'image',
                'timeout' => 60
            ]);

            return $uploadedFile->getSecurePath();
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'upload de l\'image: ' . $e->getMessage());
        }
    }

    /**
     * Upload un fichier audio vers Cloudinary
     */
    public function uploadAudio($file)
    {
        try {
            // Augmenter le temps d'exécution pour l'upload
            set_time_limit(180); // 3 minutes (audio files are bigger)
            
            $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'podcasts/audio',
                'resource_type' => 'video', // Cloudinary utilise 'video' pour les fichiers audio
                'timeout' => 120
            ]);

            return $uploadedFile->getSecurePath();
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'upload de l\'audio: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un fichier de Cloudinary
     */
    public function deleteFile($url)
    {
        try {
            // Extraire le public_id de l'URL
            $publicId = $this->getPublicIdFromUrl($url);
            
            if ($publicId) {
                Cloudinary::destroy($publicId);
            }
        } catch (\Exception $e) {
            // Ne pas bloquer si la suppression échoue
            \Log::warning('Erreur lors de la suppression du fichier Cloudinary: ' . $e->getMessage());
        }
    }

    /**
     * Extraire le public_id d'une URL Cloudinary
     */
    private function getPublicIdFromUrl($url)
    {
        // Exemple URL: https://res.cloudinary.com/cloud_name/image/upload/v123456/podcasts/images/abc123.jpg
        // Public ID: podcasts/images/abc123
        
        $parts = explode('/upload/', $url);
        if (count($parts) === 2) {
            $pathParts = explode('/', $parts[1]);
            array_shift($pathParts); // Enlever la version (v123456)
            $publicId = implode('/', $pathParts);
            // Enlever l'extension
            return pathinfo($publicId, PATHINFO_DIRNAME) . '/' . pathinfo($publicId, PATHINFO_FILENAME);
        }
        
        return null;
    }
}
