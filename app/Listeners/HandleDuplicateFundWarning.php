<?php

namespace App\Listeners;

use App\Events\DuplicateFundWarning;
use Illuminate\Support\Facades\Log;

class HandleDuplicateFundWarning
{
    public function handle(DuplicateFundWarning $event): void
    {
        Log::warning('Duplicate fund detected', [
            'name' => $event->name,
            'manager_id' => $event->fundManagerId
        ]);
    }
}
