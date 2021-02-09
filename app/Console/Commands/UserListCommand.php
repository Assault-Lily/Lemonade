<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UserListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-admin:list {--en : Display output in EN}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show list of users';

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
     * @return int
     */
    public function handle()
    {
        if($this->option('en') || true){ // UserCreateCommand と統一
            app()->setLocale('en');
        }

        $users = User::all();

        $this->line(__('userAdmin.list'));

        $users_table = array();
        foreach ($users as $user){
            $users_table[] = [
                $user->id,
                $user->name,
                $user->email
            ];
        }

        $this->table(
            ['ID','Name','E-mail'],
            $users_table
        );

        $this->info('Complete!');

        return self::SUCCESS;
    }
}
