<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AvnSave extends Model
{
    protected $fillable = [
        'avn_id',
        'file_url',
        'label',
        'version',
        'completed_at',
    ];

    protected static function booted()
    {
        static::saving(function ($save) {
            if (is_null($save->file_url) && $save->getOriginal('file_url')) {
                $save->file_url = $save->getOriginal('file_url');
                return;
            }

            if (str_contains($save->file_url, '/')) {
                try {
                    $url = Storage::disk('google')->url($save->file_url);
                    parse_str(parse_url($url, PHP_URL_QUERY), $queryArray);

                    if (isset($queryArray['id'])) {
                        $save->file_url = $queryArray['id'];
                    }
                } catch (\Exception $e) {
                    // Keep path as fallback
                }
            }
        });
    }
}
