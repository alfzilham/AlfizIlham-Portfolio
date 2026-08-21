<?php
/**
 * ApiController — Handles AJAX/API requests
 */
class ApiController
{
    /**
     * Handle contact form submission (fallback)
     * POST /api/contact
     */
    public function contact()
    {
        if (!Request::isPost()) {
            json_response(['error' => 'Method not allowed'], 405);
        }

        $data = Request::all();
        $result = ContactService::submit($data);

        if ($result['success']) {
            json_response($result, 200);
        } else {
            json_response($result, 422);
        }
    }

    /**
     * Get visitor count
     * GET /api/visitor
     */
    public function visitorCount()
    {
        json_response([
            'count' => VisitorService::getCount(),
            'today' => VisitorService::getTodayCount(),
        ]);
    }

    /**
     * Get tools filtered
     * GET /api/tools?category=frontend&search=react
     */
    public function tools()
    {
        $category = Request::get('category', 'all');
        $search = Request::get('search', '');

        json_response([
            'tools' => Tool::filtered($category, $search),
        ]);
    }

    /**
     * Get projects filtered
     * GET /api/projects?category=website
     */
    public function projects()
    {
        $category = Request::get('category', 'all');

        if ($category === 'all') {
            $projects = Project::all();
        } else {
            $projects = Project::byCategory($category);
        }

        json_response([
            'projects' => $projects,
        ]);
    }
}
