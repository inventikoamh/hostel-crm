<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intent extends Model
{
    use HasFactory;

    protected $fillable = [
        'intent_name',
        'intent_api_url',
        'intent_api_type',
        'intent_api_parameters',
        'intent_description',
        'module',
        'requires_auth',
    ];

    protected $casts = [
        'intent_api_parameters' => 'array',
        'requires_auth' => 'boolean',
    ];

    /**
     * Scope to get intents by module
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to get intents by API type
     */
    public function scopeByApiType($query, $type)
    {
        return $query->where('intent_api_type', $type);
    }

    /**
     * Scope to get public intents (no auth required)
     */
    public function scopePublic($query)
    {
        return $query->where('requires_auth', false);
    }

    /**
     * Scope to get authenticated intents
     */
    public function scopeAuthenticated($query)
    {
        return $query->where('requires_auth', true);
    }

    /**
     * Get all available modules
     */
    public static function getModules()
    {
        return self::distinct()->pluck('module')->filter()->sort()->values()->toArray();
    }

    /**
     * Get all available API types
     */
    public static function getApiTypes()
    {
        return self::distinct()->pluck('intent_api_type')->filter()->sort()->values()->toArray();
    }
}
