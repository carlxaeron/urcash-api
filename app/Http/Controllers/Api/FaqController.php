<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\FaqInterface;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    protected $faqInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(FaqInterface $faqInterface) {
        $this->faqInterface = $faqInterface;
    }

    /**
     * Get all faqs
     */
    public function index() {
        return $this->faqInterface->getAllFaqs();
    }

    /**
     * Get faq by ID
     */
    public function show($id) {
        return $this->faqInterface->getFaqById($id);
    }

    /**
     * Get all faqs by category
     */
    public function showFaqsByCategory($category) {
        return $this->faqInterface->getFaqsByCategory($category);
    }

    /**
     * Search faqs based on query
     */
    public function searchFaqs(Request $request) {
        return $this->faqInterface->searchFaqs($request);
    }

    /**
     * Update faq
     */
    public function update(Request $request, $id) {
        return $this->faqInterface->updateFaq($request, $id);
    }

    /**
     * Delete faq
     */
    public function delete($id) {
        return $this->faqInterface->deleteFaq($id);
    }

    /**
     * Create faq
     */
    public function create(Request $request) {
        return $this->faqInterface->createFaq($request);
    }
}
