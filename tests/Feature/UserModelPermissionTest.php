<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Units\Auth\Manage\Models\UserModel;
use Units\Shield\Manage\Models\Role;
use Units\Shield\Manage\Models\Permission;

class UserModelPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_users_with_permission_method(): void
    {
        // Create a permission
        $permission = Permission::create(['name' => 'view-users', 'guard_name' => 'manage']);

        // Create users
        $user1 = UserModel::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'phone_number' => '09123456789',
            'username' => 'johndoe',
            'national_code' => '1234567890',
            'created_by' => 'system',
            'password_hash' => 'hash'
        ]);

        $user2 = UserModel::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'phone_number' => '09123456788',
            'username' => 'janedoe',
            'national_code' => '1234567891',
            'created_by' => 'system',
            'password_hash' => 'hash'
        ]);

        // Assign permission directly to user1
        $user1->givePermissionTo($permission);

        // Create a role with the permission and assign to user2
        $role = Role::create(['name' => 'viewer', 'guard_name' => 'manage']);
        $role->givePermissionTo($permission);
        $user2->assignRole($role);

        // Test the method
        $usersWithPermission = UserModel::getUsersWithPermission('view-users');

        $this->assertCount(2, $usersWithPermission);
        $this->assertTrue($usersWithPermission->contains($user1->getKey()));
        $this->assertTrue($usersWithPermission->contains($user2->getKey()));

        // Test with limit
        $usersWithLimit = UserModel::getUsersWithPermission('view-users', 1);
        $this->assertCount(1, $usersWithLimit);

        // Test with non-existent permission
        $usersWithoutPermission = UserModel::getUsersWithPermission('non-existent-permission');
        $this->assertCount(0, $usersWithoutPermission);
    }
}
