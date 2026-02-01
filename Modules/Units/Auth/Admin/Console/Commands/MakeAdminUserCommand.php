<?php

namespace Units\Auth\Admin\Console\Commands;

use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\Console\Attribute\AsCommand;
use Units\Users\Admin\Admin\Services\UserService;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-user-admin')]
class MakeAdminUserCommand extends Command
{
    protected $description = 'Create a new Filament Admin user - customized';

    protected $signature = 'make:filament-user-admin
                            {--username= : The user name of the user}
                            {--mobile= : A valid and unique mobile number}
                            {--password= : The password for the user (min. 8 characters)}';

    /**
     * @var array{username: string|null, mobile: string|null, password: string|null}
     */
    protected array $options = [];

    protected UserService $userService;

    /**
     * Collect user data from options or prompt.
     *
     * @return array{username: string, mobile: string, password: string}
     */
    protected function getUserData(): array
    {
        $username = $this->options['username'] ?? text(
            label: 'User name',
            required: true,
        );

        $mobile = $this->options['mobile'] ?? text(
            label: 'Mobile number',
            required: true,
            validate: fn(string $mobile): ?string => match (true) {
                !preg_match('/^(\+98|0)\d{10}$/', $mobile) => 'The mobile number must be in format 09353466612 or +989353466620',
                default => null,
            },
        );

        $password = $this->options['password'] ?? password(
            label: 'Password',
            required: true,
            validate: fn(string $password): ?string => strlen($password) < 8
                ? 'Password must be at least 8 characters.'
                : null,
        );

        return [
            'username' => $username,
            'mobile' => $mobile,
            'password' => $password,
        ];
    }

    /**
     * Check if user with given username or mobile already exists.
     */
    protected function checkExists(array $data): bool
    {
        $usernameExists = $this->userService->getByUserName($data['username']);
        $mobileExists = $this->userService->getByMobile($data['mobile']);
        $hasExistsData = false;

        if (!empty($usernameExists)) {
            $hasExistsData = true;
            $this->components->error('User name "' . $data['username'] . '" already exists.');
        }
        if (!empty($mobileExists)) {
            $hasExistsData = true;
            $this->components->error('Mobile number "' . $data['mobile'] . '" already exists.');
        }

        return $hasExistsData;
    }

    /**
     * Create the user.
     */
    protected function createUser(array $data): ?Authenticatable
    {
        $this->userService->actCreate(
            $data['username'],
            $data['password'],
            $data['mobile'],
            $this->userService::STATUS_ACTIVE,
            $this->userService::CREATED_BY_SYSTEM
        );

        $response = $this->userService->getSuccessResponse();
        if ($response) {
            return $response->getData()['user'];
        }

        $errors = $this->userService->getErrorMessages();
        $this->components->error($errors[0] ?? 'Unknown error occurred.');
        return null;
    }

    /**
     * Show success message.
     */
    protected function sendSuccessMessage(Authenticatable $user): void
    {
        $identifier = $user->getAttribute('mobile') ?? $user->getAttribute('username') ?? 'User';
        $this->components->info("Success! User '{$identifier}' created.");
    }

    /**
     * Command handler.
     */
    public function handle(): int
    {
        $this->userService = new UserService();
        $this->options = $this->options();

        if (!Filament::getCurrentPanel()) {
            $this->error('Filament has not been installed yet: php artisan filament:install --panels');
            return static::INVALID;
        }

        $userData = $this->getUserData();

        if ($this->checkExists($userData)) {
            return static::FAILURE;
        }

        $user = $this->createUser($userData);
        if ($user) {
            $this->sendSuccessMessage($user);
            return static::SUCCESS;
        }

        return static::FAILURE;
    }
}
