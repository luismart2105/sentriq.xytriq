<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'sentriq:create-admin {email?}';

    protected $description = 'Crea o actualiza el usuario administrador de Sentriq';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Correo del administrador', config('sentriq.contact.email'));
        $name = $this->ask('Nombre del administrador', 'Administrador Sentriq');
        $password = $this->secret('Contraseña (mínimo 10 caracteres)');

        if (! is_string($password) || mb_strlen($password) < 10) {
            $this->error('La contraseña debe tener al menos 10 caracteres.');

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make($password)],
        );

        $this->info('Administrador creado. Acceso: '.route('admin.login'));

        return self::SUCCESS;
    }
}
