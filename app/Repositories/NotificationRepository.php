<?php

namespace App\Repositories;

use App\Notification;
use App\Interfaces\NotificationInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationRepository implements NotificationInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllNotifications()
    {
        try {
            $notifications = Notification::all();

            if ($notifications->count() < 1) {
                return $this->error("Notifications not found", 404);
            }

            return $this->success("All notifications", $notifications);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getNotificationById($id)
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) return $this->error("Notification not found", 404);

            return $this->success("Notification detail", $notification);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateNotification(Request $request, $id)
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) return $this->error("Notification not found", 404);

            $inputs = [
                'user_id' => $request->user_id,
                'notification_type_id' => $request->notification_type_id,
                'title' => $request->title,
                'message' => $request->message
            ];
            $rules = [
                'user_id' => 'nullable|exists:users,id',
                'notification_type_id' => 'nullable|exists:notification_types,id'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            if ($request->has('user_id')) {
                $notification->user_id = $request->user_id;
            }
            if ($request->has('notification_type_id')) {
                $notification->notification_type_id = $request->notification_type_id;
            }
            if ($request->has('title')) {
                $notification->title = $request->title;
            }
            if ($request->has('message')) {
                $notification->message = $request->message;
            }
            $notification->save();

            return $this->success("Notification updated", $notification);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteNotification($id)
    {
        DB::beginTransaction();
        try {
            $notification = Notification::find($id);

            if (!$notification) return $this->error("Notification not found", 404);

            $notification->delete();

            DB::commit();
            return $this->success("Notification deleted", $notification);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createNotification(Request $request)
    {
        try {
            $inputs = [
                'user_id' => $request->user_id,
                'notification_type_id' => $request->notification_type_id,
                'title' => $request->title,
                'message' => $request->message
            ];
            $rules = [
                'user_id' => 'required|exists:users,id',
                'notification_type_id' => 'required|exists:notification_types,id',
                'title' => 'required',
                'message' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $notification = Notification::create([
                'user_id' => $request->user_id,
                'notification_type_id' => $request->notification_type_id,
                'title' => $request->title,
                'message' => $request->message,
            ]);

            return $this->success("Notification created", $notification);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
