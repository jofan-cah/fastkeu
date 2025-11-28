<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $table = 'documents';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'doc_number',
        'doc_type_id',
        'documentable_type',
        'documentable_id',
        'subscription_id',
        'customer_name',
        'document_date',
        'status',
        'file_path',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'document_date' => 'date',
    ];

    /**
     * Relationship: Document Type
     */
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id', 'doc_type_id');
    }

    /**
     * Polymorphic Relationship (Subscription, Customer, dll dari BEFAST)
     */
    public function documentable()
    {
        return $this->morphTo();
    }

    /**
     * Relationship: Creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Relationship: Updater
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    /**
     * Scope: Filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('document_date', [$startDate, $endDate]);
    }
}
