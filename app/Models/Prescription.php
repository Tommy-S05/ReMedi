<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\ShareableResourceInterface;
use App\Models\Concerns\Shareable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $title
 * @property string|null $doctor_name
 * @property Carbon|null $prescription_date
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Medication> $medications
 * @property-read int|null $medications_count
 * @property-read Carbon|null $prescription_date_formatted
 * @property-read User $user
 * @property-read Collection<int, ResourceShare> $shares
 * @property-read int|null $shares_count
 * @property-read Collection<int, ResourceShare> $activeShares
 * @property-read int|null $active_shares_count
 * @property-read Collection<int, ResourceShare> $pendingShares
 * @property-read int|null $pending_shares_count
 * @property-read bool $isSharedWith
 * @property-read bool $isSharedWithEmail
 * @property-read Collection<int, User> $sharedWithUsers
 * @property-read int|null $shared_with_users_count
 * @property-read bool $canBeSharedBy
 * @property-read bool $getShareableTypeName
 * @property-read array<string, string|null> $getShareableData
 *
 * @method static \Database\Factories\PrescriptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereDoctorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription wherePrescriptionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereUserId($value)
 *
 * @mixin \Eloquent
 */
final class Prescription extends Model implements ShareableResourceInterface
{
    /** @use HasFactory<\Database\Factories\PrescriptionFactory> */
    use HasFactory, Shareable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'doctor_name',
        'prescription_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'prescription_date' => 'date:Y-m-d',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['prescription_date_formatted'];

    /**
     * Get the user that owns the prescription.
     *
     * @return BelongsTo<User, Prescription>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The medications that belong to the prescription.
     *
     * @return BelongsToMany<Medication>
     */
    public function medications(): BelongsToMany
    {
        return $this->belongsToMany(Medication::class, 'medication_prescription')
            ->withPivot(['dosage_on_prescription', 'quantity_prescribed', 'instructions_on_prescription'])
            ->withTimestamps();
    }

    /**
     * Determine if the user can share this resource.
     */
    public function canBeSharedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Get the display name of the resource type.
     */
    public function getShareableTypeName(): string
    {
        return 'Prescription'; // Corregido: Debería ser 'Prescription'
    }

    /**
     * Get additional data for the notification.
     */
    public function getShareableData(): array
    {
        return [
            'name' => $this->title, // Corregido: Debería ser 'title'
        ];
    }

    /**
     * Get the formatted prescription date.
     *
     * @return Attribute<string|null, never>
     */
    protected function prescriptionDateFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->prescription_date?->format(config('app.date_format', 'd/m/Y')),
        );
    }
}
