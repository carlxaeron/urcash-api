<?php

namespace App\Repositories;

use App\Permission;
use App\User;
use App\Interfaces\PermissionInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PermissionRepository implements PermissionInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    private $validation_rules = ['name' => 'required'];

    public function getAllPermissions()
    {
        try {
            $permissions = Permission::all();

            if ($permissions->count() < 1) {
                return $this->error("Permissions not found", 404);
            }

            return $this->success("All permissions", $permissions);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getPermissionById($id)
    {
        try {
            $permission = Permission::find($id);

            if (!$permission) return $this->error("Permission not found", 404);

            return $this->success("Permission detail", $permission);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getPermissionsByUserId($id)
    {
        try {
            $user = User::find($id);
            $user_permissions = $user->first()->permissions;

            return $this->success("User permissions", $user_permissions);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updatePermission(Request $request, $id)
    {
        try {
            $permission = Permission::find($id);

            if (!$permission) return $this->error("Permission not found", 404);

            $inputs = ['name' => $request->name];
            $validation = Validator::make($inputs, $this->validation_rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Transform permission name into a slug. eg. if permission name is "Create Users", slug is "create-users"
            $slug = Str::slug($request->name, '-');
            $find_slug = Permission::where('slug', '=', $slug)->whereNotIn('id', [$id])->first();

            if ($find_slug) {
                return $this->error("Permission update failed. Permission already exists", 500);
            }

            $permission->name = $request->name;
            $permission->slug = $slug;
            $permission->save();

            return $this->success("Permission updated", $permission);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deletePermission($id)
    {
        DB::beginTransaction();
        try {
            $permission = Permission::find($id);

            if (!$permission) return $this->error("Permission not found", 404);

            $permission->delete();

            DB::commit();
            return $this->success("Permission deleted", $permission);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createPermission(Request $request)
    {
        try {
            $inputs = ['name' => $request->name];
            $validation = Validator::make($inputs, $this->validation_rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Transform permission name into a slug. eg. if permission name is "Create Users", slug is "create-users"
            $slug = Str::slug($request->name, '-');
            $find_slug = Permission::where('slug', '=', $slug)->first();

            if ($find_slug) {
                return $this->error("Permission create failed. Permission already exists", 500);
            }

            $permission = Permission::create([
                'name' => $request->name,
                'slug' => $slug
            ]);

            return $this->success("Permission created", $permission);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
