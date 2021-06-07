<?php

namespace App\Repositories;

use App\Role;
use App\User;
use App\Interfaces\RoleInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class RoleRepository implements RoleInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    private $validation_rules = ['name' => 'required'];

    public function getAllRoles()
    {
        try {
            $roles = Role::all();

            if ($roles->count() < 1) {
                return $this->error("Roles not found", 404);
            }

            return $this->success("All roles", $roles);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getRoleById($id)
    {
        try {
            $role = Role::find($id);

            if (!$role) return $this->error("Role not found", 404);

            return $this->success("Role detail", $role);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getRolesByUserId($id)
    {
        try {
            $user = User::find($id);
            $user_roles = $user->roles;

            return $this->success("User roles", $user_roles);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function attachRoleToUserId(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) return $this->error("User not found", 404);

            $inputs = ['name' => $request->name];
            $validation = Validator::make($inputs, $this->validation_rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Transform role name into a slug. eg. if role name is "Staff Member", slug is "staff-member"
            $slug = Str::slug($request->name, '-');
            $find_slug = Role::where('slug', '=', $slug)->first();

            if (!$find_slug) {
                return $this->error("Role does not exist", 500);
            } elseif ($user->hasRole($find_slug->slug)) {
                return $this->error("Role already exists for this user", 500);
            }

            $user->roles()->attach($find_slug); // Add role to user
            $user_roles = $user->roles;

            return $this->success("User roles updated", $user_roles);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function detachRoleOfUserId(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) return $this->error("User not found", 404);

            $inputs = ['name' => $request->name];
            $validation = Validator::make($inputs, $this->validation_rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Transform role name into a slug. eg. if role name is "Staff Member", slug is "staff-member"
            $slug = Str::slug($request->name, '-');

            $find_slug = Role::where('slug', '=', $slug)->first();

            if (!$find_slug) {
                return $this->error("Role does not exist", 500);
            } elseif (!$user->hasRole($find_slug->slug)) {
                return $this->error("Role does not exist for this user", 500);
            }

            $user->roles()->detach($find_slug); // Remove role of user
            $user_roles = $user->roles;

            return $this->success("User roles updated", $user_roles);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateRole(Request $request, $id)
    {
        try {
            $role = Role::find($id);

            if (!$role) return $this->error("Role not found", 404);

            $inputs = ['name' => $request->name];
            $validation = Validator::make($inputs, $this->validation_rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Transform role name into a slug. eg. if role name is "Staff Member", slug is "staff-member"
            $slug = Str::slug($request->name, '-');
            $find_slug = Role::where('slug', '=', $slug)->whereNotIn('id', [$id])->first();

            if ($find_slug) return $this->error("Role update failed. Role already exists", 500);

            $role->name = $request->name;
            $role->slug = $slug;
            $role->save();

            return $this->success("Role updated", $role);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteRole($id)
    {
        DB::beginTransaction();
        try {
            $role = Role::find($id);

            if (!$role) return $this->error("Role not found", 404);

            $role->delete();

            DB::commit();
            return $this->success("Role deleted", $role);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createRole(Request $request)
    {
        try {
            $inputs = ['name' => $request->name];
            $validation = Validator::make($inputs, $this->validation_rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Transform role name into a slug. eg. if role name is "Staff Member", slug is "staff-member"
            $slug = Str::slug($request->name, '-');
            $find_slug = Role::where('slug', '=', $slug)->first();

            if ($find_slug) return $this->error("Role create failed. Role already exists", 500);

            $role = Role::create([
                'name' => $request->name,
                'slug' => $slug
            ]);

            return $this->success("Role created", $role);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
