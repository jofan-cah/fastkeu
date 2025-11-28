<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_name',
        'description',
    ];

    /**
     * Relationship: Users
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Relationship: Permissions
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'role_id', 'role_id');
    }
}
