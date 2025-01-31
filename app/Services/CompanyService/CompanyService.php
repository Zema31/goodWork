<?php

namespace App\Services\CompanyService;

use App\Constants\Status;
use App\Constants\Button;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;
use Throwable;

class CompanyService extends BaseService
{
    public function createCompany(array $request): array
    {
        try {
            $user = auth()->user();
            $rules = [
                'name' => 'bail|required|string',
            ];
            $messages = [
                'required' => 'Поле :attribute должно быть заполнено.',
                'string' => 'Поле :attribute должно быть строкой.',
            ];

            $validator = Validator::make($request, $rules, $messages);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return ['code' => 406, 'content' => ['message' => $errors]];
            }

            $company = Company::where([
                ['name', $request['name']],
                ['user_id', $user->id],
            ])->first();

            if ($company) {
                return ['code' => 208, 'content' => ['message' => 'У вас уже есть компания с таким названием.']];
            }

            $company = Company::create([
                'name' => $request['name'],
                'user_id' => $user->id,
                'status' => Status::ACTIVE
            ]);

            if (!$company) {
                return ['code' => 422, 'content' => ['message' => 'Произошла ошибка. Попробуйте позднее.']];
            }

            return ['code' => 200, 'content' => ['message' => 'Компания зарегистрирована.', 'data' => ['company_id' => $company->id]]];
        } catch (Throwable $e) {
            return $this->errorLog('createCompany', $e->getMessage());
        }
    }

    public function findCompaniesByUserId(): array
    {
        try {
            $user = auth()->user();
            $companies = [];
            $items = Company::where([
                ['user_id', $user->id],
            ])->with('promos')->get();
            foreach ($items as $item) {
                $promos = [];
                foreach ($item->promos as $promo) {
                    $promos[] = [
                        'title' => $promo['title'],
                        'text' => $promo['text'],
                        'status' => Status::INFO[$promo['status']],
                        'url' => $promo['url'],
                        'view_counts' => $promo['view_counts'],
                        'cpm' => $promo['cpm'],
                        'amount' => $promo['amount'],
                        'button_text' => Button::INFO[$promo['button_text']],
                        'id' => $promo['id'],
                    ];
                };
                $companies[] = [
                    'name' => $item['name'],
                    'status' => Status::INFO[$item['status']],
                    'id' => $item['id'],
                    'promos' => $promos
                ];
            }
            return ['code' => 200, 'content' => ['data' => ['companies' => $companies]]];
        } catch (Throwable $e) {
            return $this->errorLog('findCompaniesByUserId', $e->getMessage());
        }
    }

    public function editStatusCompany(array $request, string $id): array
    {
        try {
            $user = auth()->user();
            $rules = [
                'status' => [
                    'bail',
                    'required',
                    'string',
                    Rule::in([Status::ACTIVE, Status::WAIT, Status::ARCHIVE])
                ]
            ];
            $messages = [
                'required' => 'Поле :attribute должно быть заполнено.',
                'string' => 'Поле :attribute должно быть строкой.',
                'in' => 'Поле :attribute не может иметь данное значение.',
            ];

            $validator = Validator::make($request, $rules, $messages);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return ['code' => 406, 'content' => ['message' => $errors]];
            }

            $company = Company::where([
                ['id', (int) $id],
                ['user_id', $user->id],
            ])->first();

            if (!$company) {
                return ['code' => 422, 'content' => ['message' => 'Компания не найдена.']];
            }

            if (in_array($request['status'], [Status::WAIT, Status::ARCHIVE])) {
                $company->promos()->update(['status' => $request['status']]);
            }

            $company->status = $request['status'];

            $company->save();

            return ['code' => 200, 'content' => ['message' => 'Статус изменен.', 'data' => ['company_id' => $company->id]]];
        } catch (Throwable $e) {
            return $this->errorLog('editStatusCompany', $e->getMessage());
        }
    }

    public function deleteCompany(string $id): array
    {
        try {
            $user = auth()->user();
            $company = Company::where([
                ['id', (int) $id],
                ['user_id', $user->id],
            ])->first();
            if (!$company) {
                return ['code' => 422, 'content' => ['message' => 'Компания не найдена.']];
            }
            $company->promos()->delete();
            $company->delete();
            return ['code' => 200, 'content' => ['message' => 'Компания удалена.']];
        } catch (Throwable $e) {
            return $this->errorLog('deleteCompany', $e->getMessage());
        }
    }
}
