<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ReminderService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

final class SendMedicationRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-medication-reminders
                            {--user= : Send reminders for a specific user ID}
                            {--date= : Simulate a specific date/time (YYYY-MM-DD HH:MM:SS) for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scans for due medication schedules and sends reminder notifications to users.';

    /**
     * Create a new command instance.
     *
     * @param ReminderService $reminderService
     */
    public function __construct(protected ReminderService $reminderService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting to send medication reminders...');

        $simulationTime = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();
        // Redondear al minuto actual para consistencia con la ejecución de la tarea
        $currentTime = $simulationTime->second(0)->millisecond(0);

        $userId = $this->option('user');

        $usersQuery = User::query();
        if ($userId) {
            $usersQuery->where('id', $userId);
        }

        // Podrías añadir un filtro para usuarios activos o con configuraciones de recordatorio activas
        // ->where('is_active', true)

        $usersQuery->chunk(100, function ($users) use ($currentTime) {
            /** @var Collection<int, User> $users */
            foreach ($users as $user) {
                $this->info("Processing user ID: {$user->id} ({$user->name}) at {$currentTime->toDateTimeString()}");
                try {
                    $this->reminderService->sendRemindersForUser($user, $currentTime);
                } catch (Throwable $e) {
                    Log::error("Error processing reminders for user ID {$user->id}: " . $e->getMessage(), ['exception' => $e]);
                    $this->error("Error processing reminders for user ID {$user->id}. Check logs.");
                }
            }
        });

        $this->info('Medication reminders process finished.');

        return Command::SUCCESS;
    }
}
