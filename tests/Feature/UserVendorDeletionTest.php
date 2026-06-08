<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserVendorDeletionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function deleting_a_user_soft_deletes_associated_vendor()
    {
        // Create user
        $user = User::factory()->create([
            'role_id' => 3, // Vendor role
        ]);

        // Create vendor linked to user
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'store_name' => 'Test Store',
            'store_slug' => 'test-store',
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseHas('vendors', ['id' => $vendor->id]);

        // Delete user
        $user->delete();

        // User should be soft deleted
        $this->assertSoftDeleted('users', ['id' => $user->id]);

        // Associated vendor should be soft deleted as well
        $this->assertSoftDeleted('vendors', ['id' => $vendor->id]);
    }

    /** @test */
    public function force_deleting_a_user_force_deletes_associated_vendor()
    {
        // Create user
        $user = User::factory()->create([
            'role_id' => 3, // Vendor role
        ]);

        // Create vendor linked to user
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'store_name' => 'Test Store',
            'store_slug' => 'test-store',
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseHas('vendors', ['id' => $vendor->id]);

        // Force delete user
        $user->forceDelete();

        // User should be completely deleted from DB
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // Associated vendor should be completely deleted from DB as well
        $this->assertDatabaseMissing('vendors', ['id' => $vendor->id]);
    }
}
