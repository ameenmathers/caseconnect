<?php

namespace Database\Seeders;

use App\Models\Call;
use Illuminate\Database\Seeder;

class CallSeeder extends Seeder
{
    public function run(): void
    {
        Call::factory()->count(3)->completed()->create([
            'sentiment' => 'positive',
        ]);

        Call::factory()->count(2)->completed()->create([
            'sentiment' => 'negative',
        ]);

        Call::factory()->count(2)->completed()->create([
            'sentiment' => 'neutral',
        ]);

        Call::factory()->count(3)->highValue()->create();

        Call::factory()->count(2)->lowValue()->create();

        Call::factory()->count(2)->create(['status' => 'pending']);

        Call::factory()->failed()->create();
    }
}

