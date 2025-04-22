<?php

namespace App\Http\Requests;

use App\Events\DuplicateFundWarning;
use App\Models\Fund;
use Dotenv\Exception\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'start_year' => ['sometimes', 'digits:4', 'integer'],
            'fund_manager_id' => ['sometimes', 'uuid'],
            'aliases' => ['nullable', 'array'],
            'aliases.*' => ['string', 'distinct'],
            'companies' => ['nullable', 'array'],
            'companies.*' => ['uuid', 'distinct'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $fundId = $this->route('id'); // or 'fund' depending on your route
            $name = $this->input('name');
            $managerId = $this->input('fund_manager_id');

            if ($name && $managerId) {
                $existsByName = Fund::where('name', $name)
                    ->where('fund_manager_id', $managerId)
                    ->where('id', '!=', $fundId)
                    ->exists();

                $existsByAlias = \App\Models\Alias::where('name', $name)
                    ->whereHas(
                        'fund',
                        fn($q) => $q
                            ->where('fund_manager_id', $managerId)
                            ->where('id', '!=', $fundId)
                    )
                    ->exists();

                if ($existsByName || $existsByAlias) {
                    event(new DuplicateFundWarning($name, $managerId));

                    $validator->errors()->add('name', 'Duplicate fund detected.');
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'name' => ['Duplicate fund detected.'],
                    ]);
                }
            }
        });
    }
}
