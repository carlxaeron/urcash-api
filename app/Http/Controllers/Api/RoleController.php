<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\RoleInterface;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(RoleInterface $roleInterface) {
        $this->roleInterface = $roleInterface;
    }

    /**
     * Get all roles
     */
    public function index() {
        return $this->roleInterface->getAllRoles();
    }

    /**
     * Get role by ID
     */
    public function show($id) {
        return $this->roleInterface->getRoleById($id);
    }

    /**
     * Get roles by user ID
     */
    public function showRolesByUserId($id) {
        return $this->roleInterface->getRolesByUserId($id);
    }

    /**
     * Add role to user ID
     */
    public function addRoleToUserId(Request $request, $id) {
        return $this->roleInterface->attachRoleToUserId($request, $id);
    }

    /**
     * Remove role of user ID
     */
    public function removeRoleOfUserId(Request $request, $id) {
        return $this->roleInterface->detachRoleOfUserId($request, $id);
    }

    /**
     * Update role
     */
    public function update(Request $request, $id) {
        return $this->roleInterface->updateRole($request, $id);
    }

    /**
     * Delete role
     */
    public function delete($id) {
        return $this->roleInterface->deleteRole($id);
    }

    /**
     * Create role
     */
    public function create(Request $request) {
        return $this->roleInterface->createRole($request);
    }
}
