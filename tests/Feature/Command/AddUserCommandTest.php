<?php

use App\Console\Commands\AddUserCommand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('adds a user via the add:user command', function () {
    $name = 'Test User';
    $email = 'test@example.com';
    $password = 'password123';

    $this->artisan(AddUserCommand::class, ['name' => $name, 'email' => $email, 'password' => $password])
        ->expectsOutput('User created successfully')
        ->assertExitCode(0);

    $this->assertDatabaseHas('users', [
        'name' => $name,
        'email' => $email,
    ]);

    $user = User::firstWhere('email', $email);

    expect($user)->not->toBeNull()
        ->and(Hash::check($password, $user->password))->toBeTrue();
});
