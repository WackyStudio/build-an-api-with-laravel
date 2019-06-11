<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupDevEnvironment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the development environment';

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
        $this->info('Setting up development environment');
        $this->MigrateAndSeedDatabase();

        $this->createUser('John Doe', 'john@example.com', 'admin');
        $this->createUser('Jane Doe', 'jane@example.com');

        $this->info('All done. Bye!');
    }

    public function MigrateAndSeedDatabase()
    {
        $this->call('migrate:fresh');
        $this->call('db:seed');
    }

    public function createUser($name, $email, $role = 'user', $password = 'secret')
    {
        $this->info(PHP_EOL);
        $this->info("Creating {$name} $role");
        $user = factory(User::class)->create([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password' => Hash::make($password),
        ]);

        $this->createPersonalAccessClientAndTokenForUser($user);
        $this->info("Done");
    }

    /**
     * @param User $user
     */
    public function createPersonalAccessClientAndTokenForUser(User $user): void
    {
        $this->info(PHP_EOL);
        $this->info("Creating personal access client and token for {$user->name}");
        $this->CreatePersonalAccessClient($user);
        $this->CreatePersonalAccessToken($user);
        $this->info(PHP_EOL);
    }

    /**
     * @param $user
     */
    public function CreatePersonalAccessClient($user)
    {
        $this->call('passport:client', [
            '--personal' => true,
            '--name'     => 'Personal Access Client',
            '--user_id'  => $user->id
        ]);
    }

    /**
     * @param $user
     */
    public function CreatePersonalAccessToken($user)
    {
        $token = $user->createToken('Development Token');
        $this->info('Personal access token created successfully.');
        $this->warn("Personal access token:");
        $this->line($token->accessToken);
    }

}
