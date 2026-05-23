<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'name',
        'category',
        'description',
        'price',
        'image',
    ];

    /**
     * Get the full URL for the image if it's a relative path.
     * Still returns the raw value if it's already a full URL or base64.
     */
    public function getImageAttribute($value)
    {
        if (!$value) return $value;

        // If it's already a full URL or base64 data URL, return as-is
        if (str_starts_with($value, 'http') || str_starts_with($value, 'data:')) {
            return $value;
        }

        // Otherwise, assume it's a relative path and use asset()
        // If the path starts with 'images/', it refers to public/images/
        return asset($value);
    }
}
