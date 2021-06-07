<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface NotificationTypeInterface
{
    /**
     * Get all notification types
     *
     * @method  GET api/notification_types
     * @access  public
     */
    public function getAllNotificationTypes();

    /**
     * Get notification type by ID
     *
     * @param   integer $id
     * @method  GET api/notification_types/{id}
     * @access  public
     */
    public function getNotificationTypeById($id);

    /**
     * Update notification type
     *
     * @param   Request $request, integer $id
     * @method  POST    api/notification_type/{id}/update
     * @access  public
     */
    public function updateNotificationType(Request $request, $id);

    /**
     * Delete notification type
     *
     * @param   integer $id
     * @method  DELETE  api/notification_types/{id}/delete
     * @access  public
     */
    public function deleteNotificationType($id);

    /**
     * Create notification type
     *
     * @param   Request $request
     * @method  POST    api/notification_type/create
     * @access  public
     */
    public function createNotificationType(Request $request);
}
