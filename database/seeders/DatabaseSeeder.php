<?php

namespace Database\Seeders;

use App\Models\Alias;
use App\Models\Company;
use App\Models\Fund;
use App\Models\FundManager;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(2)->create();
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $manager1 = FundManager::create(['name' => 'Trading Capital']);
        $manager2 = FundManager::create(['name' => 'Canoe Investments']);

        $fund1 = Fund::create([
            'name' => 'Trading Growth Fund',
            'start_year' => 2025,
            'fund_manager_id' => $manager1->id,
        ]);

        $fund2 = Fund::create([
            'name' => 'Canoe Opportunity Fund',
            'start_year' => 2022,
            'fund_manager_id' => $manager2->id,
        ]);

        Alias::create(['name' => 'TRD', 'fund_id' => $fund1->id]);
        Alias::create(['name' => 'TRA', 'fund_id' => $fund1->id]);
        Alias::create(['name' => 'COF', 'fund_id' => $fund2->id]);

        $company1 = Company::create(['name' => 'Venture Group']);
        $company2 = Company::create(['name' => 'Equity Corp']);
        $company3 = Company::create(['name' => 'Canoe Holdings']);

        $fund1->companies()->attach([$company1->id, $company2->id]);
        $fund2->companies()->attach([$company3->id]);
    }
}
