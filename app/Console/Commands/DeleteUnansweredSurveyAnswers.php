<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\FormHistory;
use App\Models\SurveyAnswer;
use Illuminate\Console\Command;

class DeleteUnansweredSurveyAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-unanswered-survey-answers';

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
        $today = Carbon::now()->subDays(90); // i want to get 90 days before


        $deleted_unanswered_survey = SurveyAnswer::onlyTrashed()
            ->where('created_at', '<', $today)
            ->pluck('id'); // Pluck the IDs to use them in the next query
        
        // Delete the related form history records
        $delete_unanswered_survey_history = FormHistory::whereIn('survey_id', $deleted_unanswered_survey)
            ->delete();
        
        // Finally, delete the surveys themselves
        SurveyAnswer::whereIn('id', $deleted_unanswered_survey)->forceDelete();
        

        $this->info("Deleted {$deleted_unanswered_survey} unanswered survey answers. {$today}");
    }
}
