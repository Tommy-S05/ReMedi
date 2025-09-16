<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ShareStatusEnum;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ResourceShare
 *
 * @property int $id
 * @property int $owner_user_id
 * @property int|null $shared_with_user_id
 * @property string $shared_with_email
 * @property string $shareable_type
 * @property int $shareable_id
 * @property ShareStatusEnum $status
 * @property string $token
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $owner
 * @property-read User|null $sharedWithUser
 * @property-read Model|Eloquent $shareable
 */
final class ResourceShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_user_id',
        'shared_with_user_id',
        'shared_with_email',
        'shareable_id',
        'shareable_type',
        'status',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'status' => ShareStatusEnum::class,
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user who owns and shared the resource.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * Get the user with whom the resource is shared.
     */
    public function sharedWithUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }

    /**
     * Get the parent shareable model (Medication or Prescription).
     */
    public function shareable(): MorphTo
    {
        return $this->morphTo();
    }
}
