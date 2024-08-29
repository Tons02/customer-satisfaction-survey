<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\SurveyAnswer;
use Illuminate\Console\Command;

class UpdateVoucherStatus extends Command
{
    /**
     * The name and signature of the console command.
     * 
     * @var string
     */
    protected $signature = 'status:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get today's date
        $today = Carbon::now();

        SurveyAnswer::where('valid_until', '<', $today)
            ->update(['claim' => 'expired']);

        $this->info('Status updated for records with expired valid_until dates.'. $today);
    }
}
