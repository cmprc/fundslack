<?php

namespace App\Http\Requests;

use App\Events\DuplicateFundWarning;
use Dotenv\Exception\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreFundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'start_year' => ['required', 'digits:4', 'integer'],
            'fund_manager_id' => ['required', 'uuid'],
            'aliases' => ['nullable', 'array'],
            'aliases.*' => ['string', 'distinct'],
            'companies' => ['nullable', 'array'],
            'companies.*' => ['uuid', 'distinct'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $name = $this->input('name');
            $managerId = $this->input('fund_manager_id');

            if ($name && $managerId) {
                $existsByName = \App\Models\Fund::where('name', $name)
                    ->where('fund_manager_id', $managerId)
                    ->exists();

                $existsByAlias = \App\Models\Alias::where('name', $name)
                    ->whereHas('fund', fn($q) => $q->where('fund_manager_id', $managerId))
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
