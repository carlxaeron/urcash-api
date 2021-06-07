<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\NotificationInterface;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(NotificationInterface $notificationInterface) {
        $this->notificationInterface = $notificationInterface;
    }

    /**
     * Get all notifications
     */
    public function index() {
        return $this->notificationInterface->getAllNotifications();
    }

    /**
     * Get notification by ID
     */
    public function show($id) {
        return $this->notificationInterface->getNotificationById($id);
    }

    /**
     * Update notification
     */
    public function update(Request $request, $id) {
        return $this->notificationInterface->updateNotification($request, $id);
    }

    /**
     * Delete notification
     */
    public function delete($id) {
        return $this->notificationInterface->deleteNotification($id);
    }

    /**
     * Create notification
     */
    public function create(Request $request) {
        return $this->notificationInterface->createNotification($request);
    }
}
