<?php

namespace App\Repositories;

use App\Announcement;
use App\Interfaces\AnnouncementInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AnnouncementRepository implements AnnouncementInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllAnnouncements()
    {
        try {
            $announcements = Announcement::all();

            if ($announcements->count() < 1) {
                return $this->error("Announcements not found", 404);
            }

            return $this->success("All announcements", $announcements);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAnnouncementById($id)
    {
        try {
            $announcement = Announcement::find($id);

            if (!$announcement) return $this->error("Announcement not found", 404);

            return $this->success("Announcement detail", $announcement);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateAnnouncement(Request $request, $id)
    {
        try {
            $announcement = Announcement::find($id);

            if (!$announcement) return $this->error("Announcement not found", 404);

            $inputs = [
                'user_id' => $request->user_id,
                'title' => $request->title,
                'description' => $request->description
            ];
            $rules = [
                'user_id' => 'nullable|exists:users,id',
                'title' => 'unique:announcements,title',
                'description' => 'nullable'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            if ($request->has('user_id')) {
                $announcement->user_id = $request->user_id;
            }
            if ($request->has('title')) {
                $announcement->title = $request->title;
            }
            if ($request->has('description')) {
                $announcement->description = $request->description;
            }
            $announcement->save();

            return $this->success("Announcement updated", $announcement);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteAnnouncement($id)
    {
        DB::beginTransaction();
        try {
            $announcement = Announcement::find($id);

            if (!$announcement) return $this->error("Announcement not found", 404);

            $announcement->delete();

            DB::commit();
            return $this->success("Announcement deleted", $announcement);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createAnnouncement(Request $request)
    {
        try {
            $inputs = [
                'user_id' => $request->user_id,
                'title' => $request->title,
                'description' => $request->description
            ];
            $rules = [
                'user_id' => 'required|exists:users,id',
                'title' => 'required|unique:announcements,title',
                'description' => 'nullable'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $announcement = Announcement::create([
                'user_id' => $request->user_id,
                'title' => $request->title,
                'description' => $request->description
            ]);

            return $this->success("Announcement created", $announcement);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
