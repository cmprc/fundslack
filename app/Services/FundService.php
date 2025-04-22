<?php

namespace App\Services;

use App\Models\Fund;
use App\Models\Alias;
use Illuminate\Support\Facades\DB;

class FundService
{
    public function list($request)
    {
        $query = Fund::with(['manager', 'aliases', 'companies']);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('manager')) {
            $query->whereHas(
                'manager',
                fn($q) =>
                $q->where('name', 'like', '%' . $request->manager . '%')
            );
        }

        if ($request->filled('start_year')) {
            $query->where('start_year', $request->start_year);
        }

        return $query->get();
    }

    public function find(string $id): Fund
    {
        return Fund::with(['manager', 'aliases', 'companies'])->findOrFail($id);
    }

    public function create(array $data): Fund
    {
        return DB::transaction(function () use ($data) {
            $fund = Fund::create([
                'name' => $data['name'],
                'start_year' => $data['start_year'],
                'fund_manager_id' => $data['fund_manager_id'],
            ]);

            if (!empty($data['aliases'])) {
                foreach ($data['aliases'] as $alias) {
                    Alias::create([
                        'name' => $alias,
                        'fund_id' => $fund->id,
                    ]);
                }
            }

            if (!empty($data['companies'])) {
                $fund->companies()->attach($data['companies']);
            }

            return $fund->load(['manager', 'aliases', 'companies']);
        });
    }

    public function update(string $id, array $data): Fund
    {
        return DB::transaction(function () use ($id, $data) {
            $fund = Fund::findOrFail($id);

            $fund->update([
                'name' => $data['name'] ?? $fund->name,
                'start_year' => $data['start_year'] ?? $fund->start_year,
                'fund_manager_id' => $data['fund_manager_id'] ?? $fund->fund_manager_id,
            ]);

            if (array_key_exists('aliases', $data)) {
                $fund->aliases()->delete();
                foreach ($data['aliases'] as $alias) {
                    Alias::create(['name' => $alias, 'fund_id' => $fund->id]);
                }
            }

            if (array_key_exists('companies', $data)) {
                $fund->companies()->sync($data['companies']);
            }

            return $fund->load(['manager', 'aliases', 'companies']);
        });
    }

    public function delete(string $id): void
    {
        Fund::findOrFail($id)->delete();
    }

    public function findPotentialDuplicates(string $name, string $managerId)
    {
        return Fund::where('fund_manager_id', $managerId)
            ->where(function ($query) use ($name) {
                $query->where('name', $name)
                    ->orWhereHas('aliases', function ($q) use ($name) {
                        $q->where('name', $name);
                    });
            })
            ->get();
    }
}
