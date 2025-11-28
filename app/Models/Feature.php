<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'features';
    protected $primaryKey = 'feature_id';

    protected $fillable = [
        'feature_name',
        'description',
    ];

    /**
     * Relationship: Permissions
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'feature_id', 'feature_id');
    }
}
