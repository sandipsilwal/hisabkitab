<?php

namespace Tests\Unit;

use App\Models\GameType;
use App\Models\Package;
use App\Models\PlayerPackage;
use App\Models\PlayRecord;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PlayerPackageHistoryTest extends TestCase
{
    public function test_format_hours_minutes(): void
    {
        $this->assertSame('0h 0m', PlayerPackage::formatHoursMinutes(0));
        $this->assertSame('0h 45m', PlayerPackage::formatHoursMinutes(45));
        $this->assertSame('2h 15m', PlayerPackage::formatHoursMinutes(135));
        $this->assertSame('0h 0m', PlayerPackage::formatHoursMinutes(-10));
    }

    public function test_to_history_array_calculates_played_and_remaining_days_and_hours(): void
    {
        $gameType = new GameType(['game_name' => 'Skating']);
        $package = new Package([
            'time_per_day' => 60,
            'no_of_days' => 10, // 600 min = 10h 0m
            'amount' => 3000,
        ]);
        $package->setRelation('gameType', $gameType);

        $playerPackage = new PlayerPackage([
            'total_amount' => 3000,
            'package_status' => 'started',
            'remarks' => 'Paid cash',
        ]);
        $playerPackage->id = 7;
        $playerPackage->created_at = Carbon::parse('2026-11-01 08:00:00');
        $playerPackage->setRelation('package', $package);

        $token = new Token(['name' => 'T-01']);

        $rec1 = new PlayRecord([
            'start_time' => Carbon::parse('2026-11-01 09:00:00'),
            'end_time' => Carbon::parse('2026-11-01 09:45:00'),
            'actual_time' => 1, // Simulate legacy Carbon 3 bug value
            'no_of_players' => 1,
        ]);
        $rec1->id = 1;
        $rec1->setRelation('token', $token);
        $rec1->setRelation('gameType', $gameType);

        // Same day second session
        $rec2 = new PlayRecord([
            'start_time' => Carbon::parse('2026-11-01 16:00:00'),
            'end_time' => Carbon::parse('2026-11-01 16:30:00'),
            'actual_time' => 1, // Simulate legacy Carbon 3 bug value
            'no_of_players' => 1,
        ]);
        $rec2->id = 2;
        $rec2->setRelation('token', $token);
        $rec2->setRelation('gameType', $gameType);

        // Different day session
        $rec3 = new PlayRecord([
            'start_time' => Carbon::parse('2026-11-02 10:00:00'),
            'end_time' => Carbon::parse('2026-11-02 11:00:00'),
            'actual_time' => 1, // Simulate legacy Carbon 3 bug value
            'no_of_players' => 2,
        ]);
        $rec3->id = 3;
        $rec3->setRelation('token', $token);
        $rec3->setRelation('gameType', $gameType);

        $playerPackage->setRelation('playRecords', new Collection([$rec1, $rec2, $rec3]));

        $history = $playerPackage->toHistoryArray();

        $this->assertSame(7, $history['id']);
        $this->assertSame('Skating', $history['game_name']);
        $this->assertSame(135, $history['total_played_minutes']);
        $this->assertSame('2h 15m', $history['total_hours_played_formatted']);
        $this->assertSame(465, $history['remaining_minutes']);
        $this->assertSame('7h 45m', $history['remaining_hours_formatted']);
        $this->assertSame(2, $history['total_days_played']);
        $this->assertSame(8, $history['remaining_days']);
        $this->assertSame(3, $history['total_sessions']);
        $this->assertCount(3, $history['play_records']);
        $this->assertSame('2026-11-02', $history['play_records'][0]['date']);
        $this->assertSame(PlayerPackage::formatNepaliDate(Carbon::parse('2026-11-02')), $history['play_records'][0]['date_bs']);
        $this->assertNotSame('—', $history['play_records'][0]['date_bs']);
        $this->assertSame(PlayerPackage::formatNepaliDate(Carbon::parse('2026-11-01')), $history['purchased_date_bs']);
        $this->assertSame('2026-11-02T10:00', $history['play_records'][0]['start_time_input']);
        $this->assertSame('2026-11-02T11:00', $history['play_records'][0]['end_time_input']);
        $this->assertSame('1h 0m', $history['play_records'][0]['actual_time_formatted']);
        $this->assertSame('completed', $history['play_records'][0]['status']);
    }

    public function test_calculate_duration_minutes_and_resolved_actual_minutes(): void
    {
        $start = Carbon::parse('2026-11-12 10:00:00');
        $end45 = Carbon::parse('2026-11-12 10:45:00');
        $end10s = Carbon::parse('2026-11-12 10:00:10');

        $this->assertSame(45, PlayRecord::calculateDurationMinutes($start, $end45));
        $this->assertSame(1, PlayRecord::calculateDurationMinutes($start, $end10s));

        $record = new PlayRecord([
            'start_time' => $start,
            'end_time' => $end45,
            'actual_time' => 1,
        ]);
        $this->assertSame(45, $record->resolvedActualMinutes());
    }

    public function test_to_history_array_handles_empty_and_in_progress_sessions(): void
    {
        $gameType = new GameType(['game_name' => 'Roller']);
        $package = new Package([
            'time_per_day' => 30,
            'no_of_days' => 2, // 60 min
            'amount' => 500,
        ]);
        $package->setRelation('gameType', $gameType);

        $playerPackage = new PlayerPackage([
            'total_amount' => 500,
            'package_status' => 'started',
        ]);
        $playerPackage->id = 12;
        $playerPackage->setRelation('package', $package);

        $activeRec = new PlayRecord([
            'start_time' => Carbon::parse('2026-11-05 14:00:00'),
            'end_time' => null,
            'actual_time' => null,
            'no_of_players' => 1,
        ]);
        $activeRec->id = 10;

        $playerPackage->setRelation('playRecords', new Collection([$activeRec]));

        $history = $playerPackage->toHistoryArray();

        $this->assertSame(0, $history['total_played_minutes']);
        $this->assertSame('0h 0m', $history['total_hours_played_formatted']);
        $this->assertSame(60, $history['remaining_minutes']);
        $this->assertSame('1h 0m', $history['remaining_hours_formatted']);
        $this->assertSame(1, $history['total_days_played']);
        $this->assertSame(1, $history['remaining_days']);
        $this->assertSame('playing', $history['play_records'][0]['status']);
        $this->assertSame('—', $history['play_records'][0]['actual_time_formatted']);
    }

    public function test_history_modal_renders_and_list_views_contain_view_history_button(): void
    {
        $modalHtml = view('skatepark.players._history_modal')->render();
        $this->assertStringContainsString('id="viewHistoryModal"', $modalHtml);
        $this->assertStringContainsString('window.openHistoryModal', $modalHtml);
        $this->assertStringContainsString('Total Hours Played', $modalHtml);
        $this->assertStringContainsString('Total Days Played', $modalHtml);
        $this->assertStringContainsString('Remaining Days', $modalHtml);
        $this->assertStringContainsString('Remaining Hours', $modalHtml);
        $this->assertStringContainsString('datetime-local', $modalHtml);
        $this->assertStringContainsString('savePlayRecordTimes', $modalHtml);
        $this->assertStringContainsString('toggleEditPlayRecordTimes', $modalHtml);
        $this->assertStringContainsString('pkg.play_records.length - i', $modalHtml);
        $this->assertStringContainsString('rec.date_bs', $modalHtml);
        $this->assertStringContainsString('S.N.', $modalHtml);

        $indexSource = file_get_contents(resource_path('views/skatepark/players/index.blade.php'));
        $this->assertStringContainsString("route('players.history'", $indexSource);
        $this->assertStringContainsString("@include('skatepark.players._history_modal')", $indexSource);

        $showSource = file_get_contents(resource_path('views/skatepark/players/show.blade.php'));
        $this->assertStringContainsString("route('player_packages.history'", $showSource);
        $this->assertStringContainsString("@include('skatepark.players._history_modal')", $showSource);

        $currentSessionSource = file_get_contents(resource_path('views/skatepark/current_session/index.blade.php'));
        $this->assertStringContainsString("route('player_packages.history'", $currentSessionSource);
        $this->assertStringContainsString("route('player_packages.deactivate'", $currentSessionSource);
        $this->assertStringContainsString('confirmDeactivatePlayerPackage', $currentSessionSource);
        $this->assertStringContainsString("@include('skatepark.players._history_modal')", $currentSessionSource);
    }
}
