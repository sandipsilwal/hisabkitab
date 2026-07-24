<?php

namespace Database\Seeders;

use App\Models\GameType;
use App\Models\PaymentType;
use App\Models\DefaultTime;
use App\Models\Rate;
use App\Models\Token;
use App\Models\Package;
use Illuminate\Database\Seeder;

class SkateParkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Game Types
        $skateboard = GameType::create(['game_name' => 'Skateboarding', 'is_default' => true]);
        $rollerskate = GameType::create(['game_name' => 'Roller Skating', 'is_default' => false]);
        $scooter = GameType::create(['game_name' => 'Scooter', 'is_default' => false]);

        // 2. Payment Types
        $cash = PaymentType::create(['name' => 'Cash', 'is_default' => true]);
        PaymentType::create(['name' => 'Fonepay', 'is_default' => false]);
        PaymentType::create(['name' => 'eSewa', 'is_default' => false]);

        // 3. Default Times
        $dt30 = DefaultTime::create(['label' => '30 Min', 'minutes' => 30, 'display_order' => 1]);
        $dt60 = DefaultTime::create(['label' => '1 Hour', 'minutes' => 60, 'display_order' => 2]);
        $dt120 = DefaultTime::create(['label' => '2 Hours', 'minutes' => 120, 'display_order' => 3]);

        // 4. Rates (Game Type + Default Time -> Rate Amount)
        // Skateboarding
        Rate::create(['game_type_id' => $skateboard->id, 'default_time_id' => $dt30->id, 'rate' => 150]);
        Rate::create(['game_type_id' => $skateboard->id, 'default_time_id' => $dt60->id, 'rate' => 250]);
        Rate::create(['game_type_id' => $skateboard->id, 'default_time_id' => $dt120->id, 'rate' => 400]);

        // Roller Skating
        Rate::create(['game_type_id' => $rollerskate->id, 'default_time_id' => $dt30->id, 'rate' => 180]);
        Rate::create(['game_type_id' => $rollerskate->id, 'default_time_id' => $dt60->id, 'rate' => 300]);
        Rate::create(['game_type_id' => $rollerskate->id, 'default_time_id' => $dt120->id, 'rate' => 500]);

        // Scooter
        Rate::create(['game_type_id' => $scooter->id, 'default_time_id' => $dt30->id, 'rate' => 200]);
        Rate::create(['game_type_id' => $scooter->id, 'default_time_id' => $dt60->id, 'rate' => 350]);
        Rate::create(['game_type_id' => $scooter->id, 'default_time_id' => $dt120->id, 'rate' => 600]);

        // 5. Tokens
        for ($i = 1; $i <= 5; $i++) {
            Token::create([
                'name' => "SB-0{$i}",
                'game_type_id' => $skateboard->id,
                'display_order' => $i,
                'is_active' => true
            ]);
            Token::create([
                'name' => "RS-0{$i}",
                'game_type_id' => $rollerskate->id,
                'display_order' => $i,
                'is_active' => true
            ]);
            Token::create([
                'name' => "SC-0{$i}",
                'game_type_id' => $scooter->id,
                'display_order' => $i,
                'is_active' => true
            ]);
        }

        // 6. Packages
        Package::create([
            'game_type_id' => $skateboard->id,
            'time_per_day' => 60, // 60 minutes/day
            'no_of_days' => 15,
            'amount' => 2500
        ]);
        Package::create([
            'game_type_id' => $skateboard->id,
            'time_per_day' => 60,
            'no_of_days' => 30,
            'amount' => 4500
        ]);
        Package::create([
            'game_type_id' => $rollerskate->id,
            'time_per_day' => 60,
            'no_of_days' => 30,
            'amount' => 5000
        ]);
    }
}
