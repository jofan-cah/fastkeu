<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes,HasApiTokens;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'password',
        'phone_number',
        'avatar_path',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: Roles (Many-to-Many)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
            ->withTimestamps();
    }

    /**
     * Relationship: Documents Created
     */
    public function documentsCreated()
    {
        return $this->hasMany(Document::class, 'created_by', 'user_id');
    }

    /**
     * Relationship: Documents Updated
     */
    public function documentsUpdated()
    {
        return $this->hasMany(Document::class, 'updated_by', 'user_id');
    }

    /**
     * Relationship: Activity Logs
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id', 'user_id');
    }

    /**
     * Check if user has permission
     */
    public function hasPermission($featureName, $action)
    {
        $feature = Feature::where('feature_name', $featureName)->first();

        if (!$feature) {
            return false;
        }

        foreach ($this->roles as $role) {
            $permission = Permission::where('role_id', $role->role_id)
                ->where('feature_id', $feature->feature_id)
                ->first();

            if ($permission && $permission->{"can_$action"}) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->roles()->where('role_name', 'Admin')->exists();
    }
}
