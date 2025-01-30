<?php

namespace App\Services\CompanyService;

use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CompanyService
{
    public const string STATUS_ACTIVE = 'active';
    public const string STATUS_WAIT = 'wait';
    public const string STATUS_ARCHIVE = 'archive';

    public const array STATUSES = [
        self::STATUS_ACTIVE => 'Активен',
        self::STATUS_WAIT => 'В ожидании',
        self::STATUS_ARCHIVE => 'В архиве',
    ];

    public function createCompany(array $request, int $userId): array
    {
        try {
            $rules = [
                'name' => 'bail|required|string|max:255',
            ];
            $messages = [
                'required' => 'Поле :attribute должно быть заполнено.',
                'string' => 'Поле :attribute должно быть строкой.',
                'max' => 'Поле :attribute не может быть более :max символов.'
            ];

            $validator = Validator::make($request, $rules, $messages);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['code' => 406, 'data' => ['message' => $errors]];
            }

            $company = Company::where([
                ['name', $request['name']],
                ['user_id', $userId],
            ])->first();

            if ($company) {
                return ['code' => 208, 'data' => ['message' => 'У вас уже есть компания с таким названием.']];
            }

            $company = Company::create([
                'name' => $request['name'],
                'user_id' => $userId,
                'status' => self::STATUS_ACTIVE
            ]);

            if (!$company) {
                return ['code' => 422, 'data' => ['message' => 'Произошла ошибка. Попробуйте позднее.']];
            }

            return ['code' => 200, 'data' => ['message' => 'Компания зарегистрирована.', 'companyId' => $company->id]];
        } catch (Throwable $e) {
            return ['code' => 422, 'data' => ['message' => $e->getMessage()]];
        }
    }

    public function findCompanyById(string $id): array
    {
        return [];
    }

    public function findCompanyByUserId(array $request): array
    {
        return [];
    }

    public function editCompany(array $request, string $id): array
    {
        return [];
    }

    public function deleteCompany(string $id): array
    {
        return [];
    }

}
