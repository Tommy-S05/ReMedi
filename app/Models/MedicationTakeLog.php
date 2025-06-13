<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MedicationLogStatusEnum;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $medication_id
 * @property int $medication_schedule_id
 * @property MedicationLogStatusEnum $status
 * @property Carbon $scheduled_for
 * @property Carbon|null $action_taken_at
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Medication $medication
 * @property-read MedicationSchedule $schedule
 *
 * @method static \Database\Factories\MedicationTakeLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationTakeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationTakeLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationTakeLog query()
 *
 * @mixin Eloquent
 */
final class MedicationTakeLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'medication_id',
        'medication_schedule_id',
        'status',
        'scheduled_for',
        'action_taken_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'status' => MedicationLogStatusEnum::class,
        'scheduled_for' => 'datetime',
        'action_taken_at' => 'datetime',
    ];

    /**
     * Get the user that this log belongs to.
     *
     * @return BelongsTo<User, MedicationTakeLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the medication associated with this log.
     *
     * @return BelongsTo<Medication, MedicationTakeLog>
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * Get the schedule associated with this log.
     *
     * @return BelongsTo<MedicationSchedule, MedicationTakeLog>
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MedicationSchedule::class);
    }
}
