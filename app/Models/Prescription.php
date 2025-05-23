<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 *
 *
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
 * @mixin \Eloquent
 */
class Prescription extends Model
{
    /** @use HasFactory<\Database\Factories\PrescriptionFactory> */
    use HasFactory;

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
