<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\NotificationTypeInterface;
use Illuminate\Http\Request;

class NotificationTypeController extends Controller
{
    protected $notificationTypeInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(NotificationTypeInterface $notificationTypeInterface) {
        $this->notificationTypeInterface = $notificationTypeInterface;
    }

    /**
     * Get all notification types
     */
    public function index() {
        return $this->notificationTypeInterface->getAllNotificationTypes();
    }

    /**
     * Get notification type by ID
     */
    public function show($id) {
        return $this->notificationTypeInterface->getNotificationTypeById($id);
    }

    /**
     * Update notification type
     */
    public function update(Request $request, $id) {
        return $this->notificationTypeInterface->updateNotificationType($request, $id);
    }

    /**
     * Delete notification type
     */
    public function delete($id) {
        return $this->notificationTypeInterface->deleteNotificationType($id);
    }

    /**
     * Create notification type
     */
    public function create(Request $request) {
        return $this->notificationTypeInterface->createNotificationType($request);
    }
}
