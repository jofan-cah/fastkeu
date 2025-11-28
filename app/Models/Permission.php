<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'permission_id';

    protected $fillable = [
        'role_id',
        'feature_id',
        'can_create',
        'can_read',
        'can_update',
        'can_delete',
    ];

    protected $casts = [
        'can_create' => 'boolean',
        'can_read' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
    ];

    /**
     * Relationship: Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    /**
     * Relationship: Feature
     */
    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id', 'feature_id');
    }
}
