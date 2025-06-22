<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'reason',
        'additional_info',
        'status',
        'admin_notes',
        'resolved_by',
        'resolved_at',
        'review_title',
        'review_author_name',
        'product_name',
        'product_type',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the review that was reported (may be null if deleted).
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Get the user who made the report.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who resolved the report.
     */
    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope to get pending reports.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved reports.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get denied reports.
     */
    public function scopeDenied($query)
    {
        return $query->where('status', 'denied');
    }

    /**
     * Get the possible report reasons.
     */
    public static function getReasons()
    {
        return [
            'inappropriate' => 'Inappropriate Content',
            'spam' => 'Spam or Self-Promotion',
            'offensive' => 'Offensive Language',
            'fake' => 'Fake or Misleading Review',
            'duplicate' => 'Duplicate Review',
            'other' => 'Other'
        ];
    }

    /**
     * Check if the report is resolved.
     */
    public function isResolved()
    {
        return in_array($this->status, ['approved', 'denied']);
    }

    /**
     * Store review information before deletion.
     */
    public function storeReviewInfo()
    {
        if ($this->review) {
            $this->update([
                'review_title' => $this->review->title,
                'review_author_name' => $this->review->user->name,
                'product_name' => $this->review->product->name,
                'product_type' => $this->review->product->type,
            ]);
        }
    }

    /**
     * Get the review title (from stored data if review is deleted).
     */
    public function getReviewTitleAttribute()
    {
        return $this->review ? $this->review->title : $this->attributes['review_title'];
    }

    /**
     * Get the review author name (from stored data if review is deleted).
     */
    public function getReviewAuthorNameAttribute()
    {
        return $this->review ? $this->review->user->name : $this->attributes['review_author_name'];
    }

    /**
     * Get the product name (from stored data if review is deleted).
     */
    public function getProductNameAttribute()
    {
        return $this->review ? $this->review->product->name : $this->attributes['product_name'];
    }

    /**
     * Get the product type (from stored data if review is deleted).
     */
    public function getProductTypeAttribute()
    {
        return $this->review ? $this->review->product->type : $this->attributes['product_type'];
    }

    /**
     * Mark report as approved (delete review but keep report).
     */
    public function approve($adminId, $notes = null)
    {
        // Store review information before deletion
        $this->storeReviewInfo();

        $this->update([
            'status' => 'approved',
            'resolved_by' => $adminId,
            'resolved_at' => now(),
            'admin_notes' => $notes,
        ]);

        // Delete the reported review
        if ($this->review) {
            $this->review->delete();
        }
    }

    /**
     * Mark report as denied (keep review).
     */
    public function deny($adminId, $notes = null)
    {
        $this->update([
            'status' => 'denied',
            'resolved_by' => $adminId,
            'resolved_at' => now(),
            'admin_notes' => $notes,
        ]);
    }
}
