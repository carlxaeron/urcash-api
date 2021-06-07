<?php

namespace App\Repositories;

use App\NotificationType;
use App\Interfaces\NotificationTypeInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationTypeRepository implements NotificationTypeInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    private $validation_rules = ['notification_type_name' => 'required'];

    public function getAllNotificationTypes()
    {
        try {
            $notification_types = NotificationType::all();

            if ($notification_types->count() < 1) {
                return $this->error("Notification types not found", 404);
            }

            return $this->success("All notification types", $notification_types);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getNotificationTypeById($id)
    {
        try {
            $notification_type = NotificationType::find($id);

            if (!$notification_type) return $this->error("Notification type not found", 404);

            return $this->success("Notification type detail", $notification_type);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateNotificationType(Request $request, $id)
    {
        try {
            $notification_type = NotificationType::find($id);

            if (!$notification_type) return $this->error("Notification type not found", 404);

            $inputs = ['notification_type_name' => $request->notification_type_name];
            $validation = Validator::make($inputs, $this->validation_rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $notification_type->update($inputs);

            return $this->success("Notification type updated", $notification_type);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteNotificationType($id)
    {
        DB::beginTransaction();
        try {
            $notification_type = NotificationType::find($id);

            if (!$notification_type) return $this->error("Notification type not found", 404);

            $notification_type->delete();

            DB::commit();
            return $this->success("Notification type deleted", $notification_type);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createNotificationType(Request $request)
    {
        try {
            $inputs = ['notification_type_name' => $request->notification_type_name];
            $validation = Validator::make($inputs, $this->validation_rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $notification_type = NotificationType::create([
                'notification_type_name' => $request->notification_type_name,
            ]);

            return $this->success("Notification type created", $notification_type);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
