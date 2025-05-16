<?php

namespace App\Models;

use App\Enums\MedicationScheduleFrequencyEnum;
use Eloquent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\MedicationSchedule
 *
 * @property int $id
 * @property int $medication_id
 * @property Carbon $time_to_take
 * @property MedicationScheduleFrequencyEnum $frequency_type
 * @property array<array-key, int>|null $days_of_week
 * @property int|null $interval_in_days
 * @property int|null $interval_in_hours
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $frequency_type_label
 * @property-read Medication $medication
 * @method static \Database\Factories\MedicationScheduleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule whereDaysOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule whereFrequencyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule whereIntervalInDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule whereIntervalInHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule whereMedicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule whereTimeToTake($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationSchedule whereUpdatedAt($value)
 * @mixin Eloquent
 */
class MedicationSchedule extends Model
{
    /** @use HasFactory<\Database\Factories\MedicationScheduleFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'medication_id',
        'time_to_take',
        'frequency_type',
        'days_of_week',
        'interval_in_days',
        'interval_in_hours',
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'days_of_week' => 'array',
        'time_to_take' => 'datetime:H:i',
        'frequency_type' => MedicationScheduleFrequencyEnum::class,
        'is_active' => 'boolean',
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['frequency_type_label'];

    /**
     * Get the medication that this schedule belongs to.
     * Get the medication that owns the schedule.
     *
     * @return BelongsTo<Medication, MedicationSchedule>
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * Get the human-readable label for the schedule frequency type.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function frequencyTypeLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->frequency_type->label(), // Llama al método label() del Enum
        );
    }
}
