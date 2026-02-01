<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/8/25, 7:57 PM
 */

namespace Units\Users\Manage\My\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Modules\Basic\BaseKit\BaseService;
use Units\Users\Manage\My\DTOs\UserDTO;
use Units\Users\Manage\My\Enums\UserStatusEnum;
use Units\Users\Manage\My\Models\User;

/**
 * Service for managing My panel users
 */
class UserService extends BaseService
{
    /**
     * Create a new user
     *
     * return data:
     * ```
     *  [
     *      'user' => User,
     *  ]
     * ```
     *
     * @param UserDTO $userDTO The user data
     * @return self
     */
    public function actCreateUser(UserDTO $userDTO): self
    {
        DB::connection('my')->beginTransaction();
        try {
            // Check if user already exists
            $existingUser = User::where('national_code', $userDTO->national_code)
                ->where('phone_number', $userDTO->phone_number)
                ->withTrashed()
                ->first();

            if ($existingUser) {
                if ($existingUser->trashed()) {
                    // Restore the user if it was soft deleted
                    $existingUser->restore();
                    $existingUser->status = $userDTO->status->value;
                    $existingUser->validate_status = $userDTO->validate_status->value;
                    $existingUser->save();

                    $this->setSuccessResponse(['user' => $existingUser]);
                    DB::connection('my')->commit();
                    return $this;
                }

                $this->addError(
                    ['national_code' => $userDTO->national_code, 'phone_number' => $userDTO->phone_number],
                    'User with this national code and phone number already exists'
                );
                DB::connection('my')->rollBack();
                return $this;
            }

            // Create new user
            $userData = $userDTO->toArray();
            $userData['created_at'] = Carbon::now();
            
            $user = new User($userData);
            $user->save();

            $this->setSuccessResponse(['user' => $user]);
            $this->logInfo('User created successfully');
            
            DB::connection('my')->commit();
        } catch (Exception $e) {
            DB::connection('my')->rollBack();
            $this->addError(
                ['national_code' => $userDTO->national_code, 'phone_number' => $userDTO->phone_number],
                'Failed to create user: ' . $e->getMessage()
            );
        }

        return $this;
    }

    /**
     * Update an existing user
     *
     * return data:
     * ```
     *  [
     *      'user' => User,
     *  ]
     * ```
     *
     * @param int $userId The user ID
     * @param UserDTO $userDTO The updated user data
     * @return self
     */
    public function actUpdateUser(int $userId, UserDTO $userDTO): self
    {
        DB::connection('my')->beginTransaction();
        try {
            $user = User::findOrFail($userId);
            
            // Check if another user exists with the same national code and phone number
            $existingUser = User::where('national_code', $userDTO->national_code)
                ->where('phone_number', $userDTO->phone_number)
                ->where('id', '!=', $userId)
                ->first();
                
            if ($existingUser) {
                $this->addError(
                    ['national_code' => $userDTO->national_code, 'phone_number' => $userDTO->phone_number],
                    'Another user with this national code and phone number already exists'
                );
                DB::connection('my')->rollBack();
                return $this;
            }
            
            // Update user data
            $user->national_code = $userDTO->national_code;
            $user->phone_number = $userDTO->phone_number;
            $user->status = $userDTO->status->value;
            $user->validate_status = $userDTO->validate_status->value;
            $user->save();
            
            $this->setSuccessResponse(['user' => $user]);
            $this->logInfo('User updated successfully');
            
            DB::connection('my')->commit();
        } catch (ModelNotFoundException $e) {
            DB::connection('my')->rollBack();
            $this->addError(
                ['user_id' => $userId],
                'User not found'
            );
        } catch (Exception $e) {
            DB::connection('my')->rollBack();
            $this->addError(
                ['user_id' => $userId],
                'Failed to update user: ' . $e->getMessage()
            );
        }
        
        return $this;
    }

    /**
     * Get a user by ID
     *
     * return data:
     * ```
     *  [
     *      'user' => User,
     *  ]
     * ```
     *
     * @param int $userId The user ID
     * @return self
     */
    public function actGetUser(int $userId): self
    {
        try {
            $user = User::findOrFail($userId);
            $this->setSuccessResponse(['user' => $user]);
        } catch (ModelNotFoundException $e) {
            $this->addError(
                ['user_id' => $userId],
                'User not found'
            );
        } catch (Exception $e) {
            $this->addError(
                ['user_id' => $userId],
                'Failed to get user: ' . $e->getMessage()
            );
        }
        
        return $this;
    }

    /**
     * List users with pagination
     *
     * return data:
     * ```
     *  [
     *      'users' => LengthAwarePaginator,
     *  ]
     * ```
     *
     * @param int $perPage Number of items per page
     * @param array $filters Optional filters
     * @return self
     */
    public function actListUsers(int $perPage = 15, array $filters = []): self
    {
        try {
            $query = User::query();
            
            // Apply filters
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            
            if (isset($filters['validate_status'])) {
                $query->where('validate_status', $filters['validate_status']);
            }
            
            if (isset($filters['national_code'])) {
                $query->where('national_code', 'like', '%' . $filters['national_code'] . '%');
            }
            
            if (isset($filters['phone_number'])) {
                $query->where('phone_number', 'like', '%' . $filters['phone_number'] . '%');
            }
            
            // Paginate results
            $users = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            $this->setSuccessResponse(['users' => $users]);
        } catch (Exception $e) {
            $this->addError(
                ['filters' => $filters],
                'Failed to list users: ' . $e->getMessage()
            );
        }
        
        return $this;
    }

    /**
     * Activate a user
     *
     * return data:
     * ```
     *  [
     *      'user' => User,
     *  ]
     * ```
     *
     * @param int $userId The user ID
     * @return self
     */
    public function actActivateUser(int $userId): self
    {
        DB::connection('my')->beginTransaction();
        try {
            $user = User::findOrFail($userId);
            
            if ($user->status === UserStatusEnum::ACTIVE->value) {
                $this->setSuccessResponse(['user' => $user]);
                DB::connection('my')->commit();
                return $this;
            }
            
            $user->status = UserStatusEnum::ACTIVE->value;
            $user->save();
            
            // Verify the status change by refetching
            $user = User::findOrFail($userId);
            
            if ($user->status !== UserStatusEnum::ACTIVE->value) {
                throw new Exception('Failed to activate user');
            }
            
            $this->setSuccessResponse(['user' => $user]);
            $this->logInfo('User activated successfully');
            
            DB::connection('my')->commit();
        } catch (ModelNotFoundException $e) {
            DB::connection('my')->rollBack();
            $this->addError(
                ['user_id' => $userId],
                'User not found'
            );
        } catch (Exception $e) {
            DB::connection('my')->rollBack();
            $this->addError(
                ['user_id' => $userId],
                'Failed to activate user: ' . $e->getMessage()
            );
        }
        
        return $this;
    }

    /**
     * Deactivate a user
     *
     * return data:
     * ```
     *  [
     *      'user' => User,
     *  ]
     * ```
     *
     * @param int $userId The user ID
     * @return self
     */
    public function actDeactivateUser(int $userId): self
    {
        DB::connection('my')->beginTransaction();
        try {
            $user = User::findOrFail($userId);
            
            if ($user->status === UserStatusEnum::INACTIVE->value) {
                $this->setSuccessResponse(['user' => $user]);
                DB::connection('my')->commit();
                return $this;
            }
            
            $user->status = UserStatusEnum::INACTIVE->value;
            $user->save();
            
            // Verify the status change by refetching
            $user = User::findOrFail($userId);
            
            if ($user->status !== UserStatusEnum::INACTIVE->value) {
                throw new Exception('Failed to deactivate user');
            }
            
            $this->setSuccessResponse(['user' => $user]);
            $this->logInfo('User deactivated successfully');
            
            DB::connection('my')->commit();
        } catch (ModelNotFoundException $e) {
            DB::connection('my')->rollBack();
            $this->addError(
                ['user_id' => $userId],
                'User not found'
            );
        } catch (Exception $e) {
            DB::connection('my')->rollBack();
            $this->addError(
                ['user_id' => $userId],
                'Failed to deactivate user: ' . $e->getMessage()
            );
        }
        
        return $this;
    }

    /**
     * Delete a user (soft delete)
     *
     * return data:
     * ```
     *  [
     *      'success' => true,
     *  ]
     * ```
     *
     * @param int $userId The user ID
     * @param string $reason The reason for deletion
     * @param string $deletedBy Who deleted the user
     * @return self
     */
    public function actDeleteUser(int $userId, string $reason, string $deletedBy): self
    {
        DB::connection('my')->beginTransaction();
        try {
            $user = User::findOrFail($userId);
            
            $user->deleted_reason = $reason;
            $user->deleted_by = $deletedBy;
            $user->save();
            
            $user->delete();
            
            $this->setSuccessResponse(['success' => true]);
            $this->logInfo('User deleted successfully');
            
            DB::connection('my')->commit();
        } catch (ModelNotFoundException $e) {
            DB::connection('my')->rollBack();
            $this->addError(
                ['user_id' => $userId],
                'User not found'
            );
        } catch (Exception $e) {
            DB::connection('my')->rollBack();
            $this->addError(
                ['user_id' => $userId],
                'Failed to delete user: ' . $e->getMessage()
            );
        }
        
        return $this;
    }
} 