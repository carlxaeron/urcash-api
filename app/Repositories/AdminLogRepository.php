<?php

namespace App\Repositories;

use App\AdminLog;
use App\User;
use App\Interfaces\AdminLogInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminLogRepository implements AdminLogInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllAdminLogs()
    {
        try {
            $admin_logs = AdminLog::all();

            if ($admin_logs->count() < 1) {
                return $this->error("Admin logs not found", 404);
            }

            return $this->success("All admin logs", $admin_logs);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAdminLogById($id)
    {
        try {
            $admin_log = AdminLog::find($id);

            if (!$admin_log) return $this->error("Admin log not found", 404);

            return $this->success("Admin log detail", $admin_log);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createAdminLog($action, $notes = null) // NO ROUTE
    {
        try {
            if (!Auth::check()) { // If user is not logged in
                return $this->error("Unauthorized", 401);
            } elseif (Auth::user()->hasRole('administrator')) {
                $inputs = [
                    'admin_user_id' => Auth::user()->id,
                    'action' => $action,
                    'notes' => $notes
                ];
                $rules = [
                    'admin_user_id' => 'required|exists:users,id',
                    'action' => 'required',
                    'notes' => 'nullable'
                ];
                $validation = Validator::make($inputs, $rules);

                if ($validation->fails()) return $this->error($validation->errors()->all());

                $user = User::find(Auth::user()->id);
                $full_name = $user->first_name. ' ' .$user->middle_name. ' ' .$user->last_name;

                $admin_log = AdminLog::create([
                    'admin_user_id' => Auth::user()->id,
                    'full_name' => $full_name,
                    'action' => $action,
                    'notes' => $notes
                ]);

                return $this->success("Admin log created", $admin_log);
            } elseif (!Auth::user()->hasRole('administrator')) {
                return $this->error("Cannot create log because user does not have administrator role");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
