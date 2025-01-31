<?php

namespace App\Services\PromoService;

use App\Models\Promo;
use App\Models\Company;
use App\Jobs\PromoStatusJob;
use Illuminate\Support\Facades\Validator;
use App\Constants\Status;
use App\Constants\Button;
use Illuminate\Validation\Rule;
use App\Services\BaseService;
use Throwable;

class PromoService extends BaseService
{
    public function createPromo(array $request): array
    {
        try {
            $user = auth()->user();
            $rules = [
                'title' => 'bail|required|string|max:255',
                'text' => 'bail|required|string',
                'button_text' => [
                    'bail',
                    'required',
                    'string',
                    Rule::in([Button::APP, Button::LOOK, Button::DOWNLOAD, Button::DETAILS])
                ],
                'url' => 'bail|required|string',
                'view_counts' => 'bail|required|integer',
                'amount' => 'bail|required|numeric|between:0,9999999999.99',
                'company_id' => 'exists:App\Models\Company,id'
            ];

            $messages = [
                'required' => 'Поле :attribute должно быть заполнено.',
                'string' => 'Поле :attribute должно быть строкой.',
                'max' => 'Поле :attribute не может быть более :max символов.',
                'in' => 'Поле :attribute не может иметь данное значение.',
                'integer' => 'Поле :attribute должно быть целым числом.',
                'numeric' => 'Поле :attribute должно быть десятичной дробью.',
                'between' => 'Поле :attribute должно быть между 0 и 9999999999.99.',
                'exists' => 'Данная компания не найдена.',
            ];

            $validator = Validator::make($request, $rules, $messages);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return ['code' => 406, 'content' => ['message' => $errors]];
            }

            $company = Company::where([
                ['id', (int) $request['company_id']],
                ['user_id', $user->id],
            ])->first();

            if (!$company) {
                return ['code' => 422, 'content' => ['message' => 'Компания не найдена.']];
            }

            $cpm = ($request['amount'] / $request['view_counts']) * 1000.00;
            $promo = Promo::create([
                'title' => $request['title'],
                'text' => $request['text'],
                'status' => $company->status,
                'url' => $request['url'],
                'view_counts' => $request['view_counts'],
                'amount' => $request['amount'],
                'cpm' => round($cpm, 2),
                'button_text' => $request['button_text'],
                'company_id' => $request['company_id'],
            ]);

            if (!$promo) {
                return ['code' => 422, 'content' => ['message' => 'Произошла ошибка. Попробуйте позднее.']];
            }

            return ['code' => 200, 'content' => ['message' => 'Объявление создано.', 'data' => ['promo_id' => $promo->id]]];
        } catch (Throwable $e) {
            return $this->errorLog('createPromo', $e->getMessage());
        }
    }

    public function findPromosByUserId(): array
    {
        try {
            $user = auth()->user();
            $promos = [];
            $items = $user->promos()->get();
            foreach ($items as $item) {
                $promos[] = [
                    'title' => $item['title'],
                    'text' => $item['text'],
                    'status' => Status::INFO[$item['status']],
                    'url' => $item['url'],
                    'view_counts' => $item['view_counts'],
                    'cpm' => $item['cpm'],
                    'amount' => $item['amount'],
                    'button_text' => Button::INFO[$item['button_text']],
                    'id' => $item['id'],
                    'company_id' => $item['company_id'],
                ];
            };

            return ['code' => 200, 'content' => ['data' => ['promos' => $promos]]];
        } catch (Throwable $e) {
            return $this->errorLog('findPromosByUserId', $e->getMessage());
        }
    }

    public function deletePromo(string $id): array
    {
        try {
            $user = auth()->user();
            $promo = $user->promos()->where('promos.id', (int) $id)->first();
            if (!$promo) {
                return ['code' => 422, 'content' => ['message' => 'Объявление не найдено.']];
            }
            $promo->delete();
            return ['code' => 200, 'content' => ['message' => 'Объявление удалено.']];
        } catch (Throwable $e) {
            return $this->errorLog('deletePromo', $e->getMessage());
        }
    }

    public function editTextPromo(array $request, string $id): array
    {
        try {
            $user = auth()->user();
            $rules = [
                'text' => 'bail|required|string',
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

            $promo = $user->promos()->where('promos.id', (int) $id)->first();
            if (!$promo) {
                return ['code' => 422, 'content' => ['message' => 'Объявление не найдено.']];
            }

            $promo->text = $request['text'];
            $promo->status = Status::WAIT;

            $promo->save();

            PromoStatusJob::dispatch($promo, Status::ACTIVE)->delay(now()->addMinutes(3));

            return ['code' => 200, 'content' => ['message' => 'Текст изменен.']];
        } catch (Throwable $e) {
            return $this->errorLog('editTextPromo', $e->getMessage());
        }
    }

    public function editAmountPromo(array $request, string $id): array
    {
        try {
            $user = auth()->user();
            $rules = [
                'amount' => 'bail|required|numeric|between:0,9999999999.99',
            ];

            $messages = [
                'required' => 'Поле :attribute должно быть заполнено.',
                'numeric' => 'Поле :attribute должно быть десятичной дробью.',
                'between' => 'Поле :attribute должно быть между 0 и 9999999999.99.',
            ];

            $validator = Validator::make($request, $rules, $messages);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return ['code' => 406, 'content' => ['message' => $errors]];
            }

            $promo = $user->promos()->where('promos.id', (int) $id)->first();
            if (!$promo) {
                return ['code' => 422, 'content' => ['message' => 'Объявление не найдено.']];
            }

            if ($request['amount'] > 0 && $promo->amount == 0){
                $promo->status = Status::ACTIVE;
            }

            if ($request['amount'] == 0){
                $promo->status = Status::WAIT;
            }

            $promo->amount = $request['amount'];

            $promo->save();

            return ['code' => 200, 'content' => ['message' => 'Бюджет изменен.']];
        } catch (Throwable $e) {
            return $this->errorLog('editAmountPromo', $e->getMessage());
        }
    }

}
