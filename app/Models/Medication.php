<?php

namespace App\Models;

use App\Enums\MedicationTypeEnum;
use Eloquent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use \Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Medication
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property MedicationTypeEnum|null $type
 * @property string|null $dosage
 * @property string|null $instructions
 * @property int|null $quantity
 * @property string|null $strength
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, MedicationSchedule> $schedules
 * @property-read int|null $schedules_count
 * @property-read Collection<int, Prescription> $prescriptions
 * @property-read int|null $prescriptions_count
 * @property-read string|null $type_label
 * @property-read User $user
 * @method static \Database\Factories\MedicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication whereDosage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication whereStrength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Medication whereUserId($value)
 * @mixin Eloquent
 */
class Medication extends Model
{
    /** @use HasFactory<\Database\Factories\MedicationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'dosage',
        'instructions',
        'quantity',
        'strength',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'type' => MedicationTypeEnum::class,
        'quantity' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['type_label']; // Añadir type_label aquí

    /**
     * Get the user that owns the medication.
     *
     * @return BelongsTo<User, Medication>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the schedules for the medication.
     *
     * @return HasMany<MedicationSchedule>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(MedicationSchedule::class);
    }

    /**
     * The prescriptions that include this medication.
     *
     * @return BelongsToMany<Prescription>
     */
    public function prescriptions(): BelongsToMany
    {
        return $this->belongsToMany(Prescription::class, 'medication_prescription')
            ->withPivot(['dosage_on_prescription', 'quantity_prescribed', 'instructions_on_prescription'])
            ->withTimestamps();
    }

    /**
     * Get the human-readable label for the medication type.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string|null, never>
     */
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->type?->label(),
        );
    }
}
