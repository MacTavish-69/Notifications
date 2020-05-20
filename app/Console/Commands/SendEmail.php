<?php

namespace App\Console\Commands;

use App\Jobs\sendReminderEmail;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send out an email to users who have not updated their password in last 10 days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = Carbon::today();
        $tenDaysAgo = $today->subDays(10);
        $users = User::where('updated_at', '<=', $tenDaysAgo)->get();
        if (count($users) !== 0) {
            foreach ($users as $user) {
                $id = $user->id;
                sendReminderEmail::dispatch($id)->onConnection('database')->delay(now()->addMinutes(10));
            }
        }
    }
}
