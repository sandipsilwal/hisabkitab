<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create days table
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->string('date_bs'); // if BS date is string
            $table->date('date_ad');
            $table->integer('total')->default(0);
            $table->timestamps();
        });

        // Alter transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('day_id')->nullable()->after('id');

            $table->foreign('day_id')
                ->references('id')->on('days')
                ->onDelete('set null');
        });

        $dates = DB::table('transactions')
            ->select('date_ad', 'date_bs', DB::raw('SUM(amount) as total'))
            ->groupBy('date_ad', 'date_bs')
            ->get();

        foreach ($dates as $date) {
            // Insert into days table
            $dayId = DB::table('days')->insertGetId([
                'date_bs' => $date->date_bs,
                'date_ad' => $date->date_ad,
                'total'   => $date->total,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update transactions with this day_id
            DB::table('transactions')
                ->where('date_ad', $date->date_ad)
                ->update(['day_id' => $dayId]);
        }
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['day_id']);
            $table->dropColumn('day_id');
        });

        Schema::dropIfExists('days');
    }
};
