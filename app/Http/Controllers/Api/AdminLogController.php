<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\AdminLogInterface;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    protected $adminLogInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(AdminLogInterface $adminLogInterface) {
        $this->adminLogInterface = $adminLogInterface;
    }

    /**
     * Get all admin logs
     */
    public function index() {
        return $this->adminLogInterface->getAllAdminLogs();
    }

    /**
     * Get admin log by ID
     */
    public function show($id) {
        return $this->adminLogInterface->getAdminLogById($id);
    }
}
