<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'sonumb',
        'site_name',
        'site_id',
        'operator',
        'tower_type',
        'regency',
        'inviting_date',
        'atp_date',
        'file',
        'note',
        'status',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TaskHistory::class);
    }
}
