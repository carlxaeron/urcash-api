<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface FaqInterface
{
    /**
     * Get all faqs
     *
     * @method  GET api/faqs
     * @access  public
     */
    public function getAllFaqs();

    /**
     * Get faq by ID
     *
     * @param   integer $id
     * @method  GET api/faqs/{id}
     * @access  public
     */
    public function getFaqById($id);

    /**
     * Get all faqs by category
     *
     * @param   string $category
     * @method  GET api/faqs/category/{category}
     * @access  public
     */
    public function getFaqsByCategory($category);

    /**
     * Search faqs based on query
     *
     * @param   Request $request
     * @method  POST    api/faqs/search
     * @access  public
     */
    public function searchFaqs(Request $request);

    /**
     * Update faq
     *
     * @param   Request $request, integer $id
     * @method  POST    api/faqs/{id}/update
     * @access  public
     */
    public function updateFaq(Request $request, $id);

    /**
     * Delete faq
     *
     * @param   integer $id
     * @method  DELETE  api/faqs/{id}/delete
     * @access  public
     */
    public function deleteFaq($id);

    /**
     * Create faq
     *
     * @param   Request $request
     * @method  POST    api/faqs/create
     * @access  public
     */
    public function createFaq(Request $request);
}
