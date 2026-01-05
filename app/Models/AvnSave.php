<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yaza\LaravelGoogleDriveStorage\Gdrive;

class AvnSave extends Model
{
    protected $fillable = [
        'avn_id',
        'file_url',
        'file_path',
        'label',
        'version',
        'completed_at',
    ];

    protected static function booted()
    {
        static::saving(function ($save) {
            if (is_null($save->file_url) && $save->getOriginal('file_url')) {
                $save->file_url  = $save->getOriginal('file_url');
                $save->file_path = $save->getOriginal('file_path'); // Keep path too
                return;
            }

            if (str_contains($save->file_url, '/')) {
                try {
                    $save->file_path = $save->file_url;
                    $url             = Storage::disk('google')->url($save->file_url);
                    parse_str(parse_url($url, PHP_URL_QUERY), $queryArray);

                    if (isset($queryArray['id'])) {
                        $save->file_url = $queryArray['id'];
                    }
                } catch (\Exception $e) {
                    Log::error("Gdrive Path Processing Error: " . $e->getMessage());
                }
            }
        });

        static::deleting(function ($save) {
            if ($save->file_path) {
                try {
                    Gdrive::delete($save->file_path);
                } catch (\Exception $e) {
                    Log::error("Gdrive Delete by Path failed: " . $e->getMessage());
                }
            } elseif ($save->file_url) {
                try {
                    Storage::disk('google')->delete($save->file_url);
                } catch (\Exception $e) {
                    Log::error("Gdrive Delete by ID failed: " . $e->getMessage());
                }
            }
        });
    }
}
