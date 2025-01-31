<?php

namespace App\Services\UserService;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserService extends BaseService
{
    public function loginUser(array $request): array
    {
        try {
            $rules = [
                'email' => 'bail|required|string|email|max:255',
                'password' => 'bail|required|string|max:255'
            ];
            $messages = [
                'required' => 'Поле :attribute должно быть заполнено.',
                'string' => 'Поле :attribute должно быть строкой.',
                'max' => 'Поле :attribute не может быть более :max символов.'
            ];
            $validator = Validator::make($request, $rules, $messages);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return ['code' => 406, 'content' => ['message' => $errors]];
            }

            $user = User::where('email', $request['email'])->first();

            if (! $user || ! Hash::check($request['password'], $user->password)) {
                return ['code' => 401, 'content' => ['message' => 'email или пароль указаны неверно.']];
            }

            return [
                'code' => 200,
                'content' => [
                    'message' => 'Пользователь зарегистрирован.',
                    'data' => [
                        'token' => $user->createToken('TEST_AUTH')->plainTextToken,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ]
            ];
        } catch (Throwable $e) {
            return $this->errorLog('loginUser', $e->getMessage());
        }
    }

    public function signupUser(array $request): array
    {
        try {
            $rules = [
                'email' => 'bail|required|string|max:255|email|unique:users,email',
                'password' => 'bail|required|string|max:255',
                'name' => 'bail|required|string|max:255',
            ];
            $messages = [
                'required' => 'Поле :attribute должно быть заполнено.',
                'email' => 'Неверно указан email.',
                'unique' => 'Данный email уже зарегистрирован.',
                'string' => 'Поле :attribute должно быть строкой.',
                'max' => 'Поле :attribute не может быть более :max символов.'
            ];

            $validator = Validator::make($request, $rules, $messages);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return ['code' => 406, 'content' => ['message' => $errors]];
            }

            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' =>  Hash::make($request['password']),
            ]);

            if (!$user) {
                return ['code' => 422, 'content' => ['message' => 'Произошла ошибка. Попробуйте позднее.']];
            }

            return [
                'code' => 200,
                'content' => [
                    'message' => 'Пользователь зарегистрирован.',
                    'data' => [
                        'token' => $user->createToken('TEST_AUTH')->plainTextToken,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ]
            ];
        } catch (Throwable $e) {
            return $this->errorLog('signupUser', $e->getMessage());
        }
    }

    public function logoutUser()
    {
        try {
            auth()->user()->tokens()->delete();
            return ['code' => 200, 'content' => ['message' => 'Пользователь вышел из системы.']];
        } catch (Throwable $e) {
            return $this->errorLog('logoutUser', $e->getMessage());
        }
    }

    public function deleteUser()
    {
        try {
            $user = auth()->user();
            $user->promos()->delete();
            $user->companies()->delete();
            $user->tokens()->delete();
            $user->delete();
            return ['code' => 200, 'content' => ['message' => 'Пользователь удален.']];
        } catch (Throwable $e) {
            return $this->errorLog('deleteUser', $e->getMessage());
        }
    }
}
