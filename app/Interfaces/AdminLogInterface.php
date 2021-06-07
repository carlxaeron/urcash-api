<?php

namespace App\Interfaces;

interface AdminLogInterface
{
    /**
     * Get all admin logs
     *
     * @method  GET api/admin_logs
     * @access  public
     */
    public function getAllAdminLogs();

    /**
     * Get admin log by ID
     *
     * @param   integer $id
     * @method  GET api/admin_logs/{id}
     * @access  public
     */
    public function getAdminLogById($id);

    /**
     * Create admin log
     *
     * @param   string $action, string $notes
     * @method  POST api/admin_logs/create
     * @access  public
     */
    public function createAdminLog($action, $notes = null);
}
