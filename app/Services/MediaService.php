<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Psr\Log\LoggerInterface;
use Spatie\MediaLibrary\HasMedia;

class MediaService extends Service
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }

    /**
     * Attach a media file to a model.
     */
    public function attachMedia(HasMedia $model, UploadedFile $file, string $collectionName): void
    {
        try {
            // Livewire TemporaryUploadedFile requires special handling:
            // store to local disk first, then pass to Spatie via addMediaFromDisk
            if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $tmpPath = $file->store('livewire-uploads-tmp', 'local');
                $model->addMediaFromDisk($tmpPath, 'local')
                      ->usingFileName($file->getClientOriginalName())
                      ->toMediaCollection($collectionName);
                Storage::disk('local')->delete($tmpPath);
            } else {
                $model->addMedia($file)
                      ->toMediaCollection($collectionName);
            }

            $this->logger->info("Media attached.", [
                'model_type' => get_class($model),
                'model_id'   => $model->id,
                'collection' => $collectionName,
            ]);
        } catch (\Exception $e) {
            $this->logger->error("Failed to attach media.", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Clear existing media from a collection and attach new.
     */
    public function syncMedia(HasMedia $model, UploadedFile $file, string $collectionName): void
    {
        $model->clearMediaCollection($collectionName);
        $this->attachMedia($model, $file, $collectionName);
    }
}
