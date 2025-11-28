<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'document_types';
    protected $primaryKey = 'doc_type_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'doc_type_id',
        'code',
        'name',
        'prefix',
        'format_code',
        'current_number',
        'current_month',
        'current_year',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'current_number' => 'integer',
    ];

    /**
     * Relationship: Documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'doc_type_id', 'doc_type_id');
    }

    /**
     * Get next document number (preview)
     */
    public function getNextNumber()
    {
        return $this->current_number + 1;
    }
}
