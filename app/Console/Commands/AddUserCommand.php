<?php

namespace App\Console\Commands;

use App\Enums\Buddy;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AddUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:user {name} {email} {password} {buddy?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new user to the application';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $buddy = $this->argument('buddy');

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'buddy' => $buddy ?? Buddy::AGAHH->value,
        ]);

        $this->info('User created successfully');
    }
}
