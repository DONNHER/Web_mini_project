<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AISecurityWorkflowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_admin_can_view_the_ai_security_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.ai-security.index'));

        $response->assertStatus(200);
        $response->assertSee('AI Fraud Detection & Security Dashboard');
    }

    #[Test]
    public function an_admin_can_approve_a_flagged_order()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::create([
            'user_id' => $admin->id,
            'total_amount' => 5000,
            'status' => 'flagged',
            'shipping_address' => '123 Suspicious St'
        ]);

        $response = $this->actingAs($admin)->post(route('admin.ai-security.resolve', $order), [
            'action' => 'approve'
        ]);

        $response->assertRedirect();
        $this->assertEquals('pending', $order->fresh()->status);
        $response->assertSessionHas('success', 'Order has been approved and moved back to processing.');
    }

    #[Test]
    public function an_admin_can_cancel_a_flagged_order()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::create([
            'user_id' => $admin->id,
            'total_amount' => 5000,
            'status' => 'flagged',
            'shipping_address' => '123 Suspicious St'
        ]);

        $response = $this->actingAs($admin)->post(route('admin.ai-security.resolve', $order), [
            'action' => 'cancel'
        ]);

        $response->assertRedirect();
        $this->assertEquals('cancelled', $order->fresh()->status);
        $response->assertSessionHas('success', 'Order has been cancelled due to fraud risk.');
    }

    #[Test]
    public function a_non_admin_cannot_access_the_ai_security_dashboard()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.ai-security.index'));

        // Assuming you have an admin middleware that redirects or errors
        $response->assertStatus(403);
    }
}
