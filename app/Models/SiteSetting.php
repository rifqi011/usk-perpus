<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'site_tagline',
        'site_description',
        'site_logo',
        'site_favicon',
        'contact_email',
        'contact_phone',
        'contact_address',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'youtube_url',
        'opening_time',
        'closing_time',
        'opening_days',
    ];

    protected $casts = [
        'opening_days' => 'array',
    ];

    /**
     * Get the singleton instance
     */
    public static function getInstance(): self
    {
        $setting = self::first();
        
        if (!$setting) {
            $setting = self::create([
                'site_name' => 'Perpustakaan',
                'opening_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            ]);
        }
        
        return $setting;
    }
}
