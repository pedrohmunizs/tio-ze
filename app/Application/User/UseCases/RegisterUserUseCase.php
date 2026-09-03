<?php

namespace App\Application\User\UseCases;

use App\Application\Driver\DTOs\CreateDriverData;
use App\Application\Driver\UseCases\CreateDriverUseCase;
use App\Application\Provider\DTOs\CreateProviderData;
use App\Application\Provider\UseCases\CreateProviderUseCase;
use App\Application\User\DTOs\CreateUserData;
use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Address\Services\AddressService;
use App\Domain\User\ValueObjects\CPF;
use App\Infrastructure\User\Models\UserModel;
use Spatie\Permission\Models\Role;

class RegisterUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private CreateProviderUseCase $createProviderUseCase,
        private CreateDriverUseCase $createDriverUseCase,
        private AddressService $address_service,
    ) {}

    public function execute(CreateUserData $data): User
    {
        $email = new Email($data->email);
        $cpf = new CPF($data->cpf);

        if ($this->repository->existsByEmail($email)) {
            throw new \DomainException('Email already registered');
        }

        if ($this->repository->existsByCpf($cpf)) {
            throw new \DomainException('CPF already registered');
        }

        $address_id = $this->address_service->createAddressFromData(
            $data->zip_code,
            $data->street,
            $data->number,
            $data->neighborhood,
            $data->city,
            $data->state,
            $data->complement,
        );

        $user = new User(
            name: $data->name,
            email: $email,
            cpf: $cpf,
            phone: $data->phone,
            password: $data->password,
            addressId: $address_id,
        );

        $this->repository->save($user);

        switch ($data->role) {
            case 'provider':
                $provider_dto = new CreateProviderData(
                    name: $data->name,
                    phone: $data->phone,
                    zip_code: $data->zip_code,
                    street: $data->street,
                    number: $data->number,
                    complement: $data->complement,
                    neighborhood: $data->neighborhood,
                    city: $data->city,
                    state: $data->state,
                    fk_user: $user->getId(),
                );

                $this->createProviderUseCase->execute($provider_dto);
                break;
            case 'driver':
                $provider_dto = new CreateProviderData(
                    name: $data->name,
                    phone: $data->phone,
                    zip_code: $data->zip_code,
                    street: $data->street,
                    number: $data->number,
                    complement: $data->complement,
                    neighborhood: $data->neighborhood,
                    city: $data->city,
                    state: $data->state,
                    fk_user: $user->getId(),
                    is_autonomous: true,
                );

                $provider = $this->createProviderUseCase->execute($provider_dto);

                $driver_dto = new CreateDriverData(
                    zip_code: $data->zip_code,
                    street: $data->street,
                    number: $data->number,
                    neighborhood: $data->neighborhood,
                    city: $data->city,
                    state: $data->state,
                    fk_user: $user->getId(),
                    fk_provider: $provider->getId(),
                    is_autonomous: true,
                    complement: $data->complement,
                );

                $this->createDriverUseCase->execute($driver_dto);

                break;
            default:
                # code...
                break;
        }

        $this->assignRoleToUser($user, $data->role ?? 'parent');

        // 5. Disparar evento (opcional)
        // event(new UserRegisteredEvent($user));

        return $user;
    }

    private function assignRoleToUser(User $user, string $roleName): void
    {
        $userModel = UserModel::find($user->getId());
        
        if (!$userModel) {
            throw new \RuntimeException('User not found after registration');
        }

        $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
        
        if (!$role) {
            $role = Role::where('name', 'parent')->where('guard_name', 'web')->first();
        }

        if ($role) {
            $userModel->assignRole($role);
        }
    }
}