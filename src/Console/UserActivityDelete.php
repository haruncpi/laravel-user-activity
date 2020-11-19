<?php

namespace Haruncpi\LaravelUserActivity\Console;

use Haruncpi\LaravelUserActivity\Models\Log;
use Illuminate\Console\Command;

class UserActivityDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-activity:delete {delete_limit?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will delete user log activity data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $deleteLimit = $this->argument('delete_limit');
        switch (strtolower(trim($deleteLimit))) {
            case 'all':
                Log::truncate();
                $this->info("All log data deleted!");
                break;
            default:
                if (is_numeric($deleteLimit)) {
                    Log::whereRaw('log_date < NOW() - INTERVAL ? DAY', [$deleteLimit])->delete();
                    $this->info("Successfully deleted log data older than $deleteLimit days");
                } else {
                    $dayLimit = config('user-activity.delete_limit');
                    Log::whereRaw('log_date < NOW() - INTERVAL ? DAY', [$dayLimit])->delete();
                    $this->info("Successfully deleted log data older than $dayLimit days");
                }
        }

    }


}
