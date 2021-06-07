<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\PermissionInterface;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $permissionInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(PermissionInterface $permissionInterface) {
        $this->permissionInterface = $permissionInterface;
    }

    /**
     * Get all permissions
     */
    public function index() {
        return $this->permissionInterface->getAllPermissions();
    }

    /**
     * Get permission by ID
     */
    public function show($id) {
        return $this->permissionInterface->getPermissionById($id);
    }

    /**
     * Get permissions by user ID
     */
    public function showPermissionsByUserId($id) {
        return $this->permissionInterface->getPermissionsByUserId($id);
    }

    /**
     * Update permission
     */
    public function update(Request $request, $id) {
        return $this->permissionInterface->updatePermission($request, $id);
    }

    /**
     * Delete permission
     */
    public function delete($id) {
        return $this->permissionInterface->deletePermission($id);
    }

    /**
     * Create permission
     */
    public function create(Request $request) {
        return $this->permissionInterface->createPermission($request);
    }
}
