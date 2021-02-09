<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UserCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-admin:create {--en : Display output in EN} {--dry-run : For test. It does not actually add any users.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        if($this->option('en') || true){ // なんでかask後に日本語こわれる
            app()->setLocale('en');
        }

        if($this->option('dry-run')){
            $this->comment(__('userAdmin.dry-run').PHP_EOL);
        }

        $new_user['name'] = $this->ask(__('userAdmin.create.name'));
        $new_user['mail'] = $this->ask(__('userAdmin.create.email'));

        $validator = \Validator::make($new_user,[
            'name' => 'required',
            'mail' => 'required|email'
        ]);
        if($validator->fails()){
            $this->error('Validation Error!');
            $this->line('Try again.');
        }

        $this->newLine();
        $this->line('  Name : '.$new_user['name']);
        $this->line('E-Mail : '.$new_user['mail']);

        $password = Str::random(9);

        if($this->confirm(__('userAdmin.confirm'))){
            $user = new User();
            $user->name = $new_user['name'];
            $user->email = $new_user['mail'];
            $user->password = \Hash::make($password);

            if(!$this->option('dry-run')){
                $user->save();
                $this->info('Saved! User ID : '.$user->id);
            }

            $this->info(__('userAdmin.create.complete'));
            $this->newLine();
            $this->line(__('userAdmin.create.password'));
            $this->newLine();
            $this->line('Password : '.$password);
        }else{
            $this->warn('Canceled.');
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
