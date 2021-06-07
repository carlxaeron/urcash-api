<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\AnnouncementInterface;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    protected $announcementInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(AnnouncementInterface $announcementInterface) {
        $this->announcementInterface = $announcementInterface;
    }

    /**
     * Get all announcements
     */
    public function index() {
        return $this->announcementInterface->getAllAnnouncements();
    }

    /**
     * Get announcement by ID
     */
    public function show($id) {
        return $this->announcementInterface->getAnnouncementById($id);
    }

    /**
     * Update announcement
     */
    public function update(Request $request, $id) {
        return $this->announcementInterface->updateAnnouncement($request, $id);
    }

    /**
     * Delete announcement.
     */
    public function delete($id) {
        return $this->announcementInterface->deleteAnnouncement($id);
    }

    /**
     * Create announcement
     */
    public function create(Request $request) {
        return $this->announcementInterface->createAnnouncement($request);
    }
}
