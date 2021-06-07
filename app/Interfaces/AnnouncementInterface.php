<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface AnnouncementInterface
{
    /**
     * Get all announcements
     *
     * @method  GET api/announcements
     * @access  public
     */
    public function getAllAnnouncements();

    /**
     * Get announcement by ID
     *
     * @param   integer $id
     * @method  GET api/announcements/{id}
     * @access  public
     */
    public function getAnnouncementById($id);

    /**
     * Update announcement
     *
     * @param   Request $request, integer $id
     * @method  POST    api/announcement/{id}/update
     * @access  public
     */
    public function updateAnnouncement(Request $request, $id);

    /**
     * Delete announcement
     *
     * @param   integer $id
     * @method  DELETE  api/announcements/{id}/delete
     * @access  public
     */
    public function deleteAnnouncement($id);

    /**
     * Create announcement
     *
     * @param   Request $request
     * @method  POST    api/announcement/create
     * @access  public
     */
    public function createAnnouncement(Request $request);
}
