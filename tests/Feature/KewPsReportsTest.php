<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KewPsReportsTest extends TestCase
{
    /**
     * Test that all 36 KEW.PS report print views render successfully with HTTP 200.
     */
    public function test_all_36_kew_ps_forms_render_successfully(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        for ($i = 1; $i <= 36; $i++) {
            $formSlug = 'kew-ps-' . $i;
            $response = $this->actingAs($user)->get(route('reports.form', $formSlug));

            $response->assertStatus(200);
            $response->assertSee('KEW.PS');
        }
    }
}
