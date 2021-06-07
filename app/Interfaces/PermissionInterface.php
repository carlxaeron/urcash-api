<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface PermissionInterface
{
    /**
     * Get all permissions
     *
     * @method  GET api/permissions
     * @access  public
     */
    public function getAllPermissions();

    /**
     * Get permission by ID
     *
     * @param   integer $id
     * @method  GET api/permissions/{id}
     * @access  public
     */
    public function getPermissionById($id);

    /**
     * Get permissions by user ID
     *
     * @param   integer $id
     * @method  GET api/permissions/users/{id}
     * @access  public
     */
    public function getPermissionsByUserId($id);

    /**
     * Update permission
     *
     * @param   Request $request, integer $id
     * @method  POST    api/permission/{id}/update
     * @access  public
     */
    public function updatePermission(Request $request, $id);

    /**
     * Delete permission
     *
     * @param   integer $id
     * @method  DELETE  api/permissions/{id}/delete
     * @access  public
     */
    public function deletePermission($id);

    /**
     * Create permission
     *
     * @param   Request $request
     * @method  POST    api/permission/create
     * @access  public
     */
    public function createPermission(Request $request);
}
