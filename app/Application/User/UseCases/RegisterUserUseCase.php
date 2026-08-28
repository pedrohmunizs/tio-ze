<?php

namespace App\Application\User\UseCases;

use App\Application\Provider\DTOs\CreateProviderData;
use App\Application\Provider\UseCases\CreateProviderUseCase;
use App\Application\User\DTOs\CreateUserData;
use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Address\Entities\Address;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Domain\User\ValueObjects\CPF;
use App\Infrastructure\User\Models\UserModel;
use Spatie\Permission\Models\Role;

class RegisterUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private AddressRepositoryInterface $addressRepository,
        private CreateProviderUseCase $createProviderUseCase,
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

        $address_id = $this->processAddress($data);

        $user = new User(
            name: $data->name,
            email: $email,
            cpf: $cpf,
            phone: $data->phone,
            password: $data->password,
            addressId: $address_id,
        );

        // 4. Persistir
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
                # code...
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

    private function processAddress(CreateUserData $data): ?int
    {
        if (!$data->zip_code) {
            return null;
        }

        $address = new Address(
            zipCode: $data->zip_code,
            street: $data->street,
            number: $data->number,
            complement: $data->complement,
            neighborhood: $data->neighborhood,
            city: $data->city,
            state: $data->state,
        );

        return $this->addressRepository->save($address);
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