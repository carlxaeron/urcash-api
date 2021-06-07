<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface NotificationInterface
{
    /**
     * Get all notifications
     *
     * @method  GET api/notifications
     * @access  public
     */
    public function getAllNotifications();

    /**
     * Get notification by ID
     *
     * @param   integer $id
     * @method  GET api/notifications/{id}
     * @access  public
     */
    public function getNotificationById($id);

    /**
     * Update notification
     *
     * @param   Request $request, integer $id
     * @method  POST    api/notification/{id}/update
     * @access  public
     */
    public function updateNotification(Request $request, $id);

    /**
     * Delete notification
     *
     * @param   integer $id
     * @method  DELETE  api/notifications/{id}/delete
     * @access  public
     */
    public function deleteNotification($id);

    /**
     * Create notification
     *
     * @param   Request $request
     * @method  POST    api/notification/create
     * @access  public
     */
    public function createNotification(Request $request);
}
