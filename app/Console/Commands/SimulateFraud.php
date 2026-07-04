<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Services\AI\FraudDetectionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SimulateFraud extends Command
{
    protected $signature = 'app:simulate-fraud {--orders=3 : Number of scenarios to run}';
    protected $description = 'Generate a variety of suspicious and normal orders to test AI Security coverage';

    public function handle(FraudDetectionService $fraudService)
    {
        $this->info("🔄 Initializing Security Overlap Simulation...");

        $user = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        // Scenario 1: The "Whale" (High Price)
        $this->comment("Simulating Scenario 1: High-Value Transaction...");
        $order1 = Order::create([
            'user_id' => $user->id,
            'total_amount' => 45000.00,
            'shipping_address' => 'Hidden Bunker, Arctic Circle',
            'status' => 'pending',
            'payment_method' => 'card'
        ]);
        $res1 = $fraudService->analyzeOrder($order1, '103.21.244.5');
        if ($res1['category'] === 'High' || $res1['score'] > 70) {
            $order1->update(['status' => 'flagged']);
            $this->line("   -> Result: FLAGGED (High Risk)");
        }

        // Scenario 2: Geolocation Mismatch
        $this->comment("Simulating Scenario 2: Geolocation Anomaly...");
        $order2 = Order::create([
            'user_id' => $user->id,
            'total_amount' => 120.00,
            'shipping_address' => 'London, UK',
            'status' => 'pending',
            'payment_method' => 'card'
        ]);
        $res2 = $fraudService->analyzeOrder($order2, '185.156.177.12');
        if ($res2['category'] === 'High' || $res2['score'] > 60) {
            $order2->update(['status' => 'flagged']);
            $this->line("   -> Result: FLAGGED (Risk detected)");
        }

        // Scenario 3: Normal Behavior
        $this->comment("Simulating Scenario 3: Legitimate Transaction...");
        $order3 = Order::create([
            'user_id' => $user->id,
            'total_amount' => 35.50,
            'shipping_address' => '123 Main St, Local City',
            'status' => 'pending',
            'payment_method' => 'card'
        ]);
        $res3 = $fraudService->analyzeOrder($order3, '127.0.0.1');
        $this->line("   -> Result: APPROVED (Low Risk)");

        $count = DB::table('ai_security_logs')->count();
        $this->info("✅ Simulation Complete. Total Scans in DB: $count");
        $this->line("Refresh your browser at: http://localhost:8000/admin/ai-security");
    }
}
