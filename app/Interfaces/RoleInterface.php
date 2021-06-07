<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface RoleInterface
{
    /**
     * Get all roles
     *
     * @method  GET api/roles
     * @access  public
     */
    public function getAllRoles();

    /**
     * Get role by ID
     *
     * @param   integer $id
     * @method  GET api/roles/{id}
     * @access  public
     */
    public function getRoleById($id);

    /**
     * Get roles by user ID
     *
     * @param   integer $id
     * @method  GET api/roles/users/{id}
     * @access  public
     */
    public function getRolesByUserId($id);

    /**
     * Add role to user ID
     *
     * @param   Request $request, integer $id
     * @method  POST    api/roles/users/{id}/add
     * @access  public
     */
    public function attachRoleToUserId(Request $request, $id);

    /**
     * Remove role of user ID
     *
     * @param   Request $request, integer $id
     * @method  POST    api/roles/users/{id}/delete
     * @access  public
     */
    public function detachRoleOfUserId(Request $request, $id);

    /**
     * Update role
     *
     * @param   Request $request, integer $id
     * @method  POST    api/roles/{id}/update
     * @access  public
     */
    public function updateRole(Request $request, $id);

    /**
     * Delete role
     *
     * @param   integer $id
     * @method  DELETE  api/roles/{id}/delete
     * @access  public
     */
    public function deleteRole($id);

    /**
     * Create role
     *
     * @param   Request $request
     * @method  POST    api/roles/create
     * @access  public
     */
    public function createRole(Request $request);
}
