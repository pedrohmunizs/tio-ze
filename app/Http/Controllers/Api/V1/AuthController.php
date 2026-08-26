<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\User\DTOs\CreateUserData;
use App\Application\User\UseCases\GetUserUseCase;
use App\Application\User\UseCases\RegisterUserUseCase;
use App\Http\Controllers\Controller;
use App\Infrastructure\User\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private RegisterUserUseCase $registerUserUseCase,
        private GetUserUseCase $getUserUseCase,
    ) {}

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'cpf' => 'required|string|size:11|unique:users',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:admin,provider,driver,parent',
                'zip_code' => 'required|string|max:255',
                'street' => 'required|string|max:255',
                'number' => 'required|string|max:255',
                'neighborhood' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:2',
            ]);
    
            $dto = CreateUserData::fromRequest($request);
            $user = $this->registerUserUseCase->execute($dto);
    
            $userModel = UserModel::where('email', $user->getEmail()->getValue())->first();
            $token = $userModel->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user->toArray(),
                'token' => $token,
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during registration',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = UserModel::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'User account is not active',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Converter para entidade de domínio para resposta
        $userEntity = $this->getUserUseCase->execute($user->id);

        return response()->json([
            'user' => $userEntity->toArray(),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        $userEntity = $this->getUserUseCase->execute($request->user()->id);
        return response()->json($userEntity->toArray());
    }
}
