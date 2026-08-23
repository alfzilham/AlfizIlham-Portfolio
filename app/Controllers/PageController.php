<?php
/**
 * PageController — Renders the main portfolio page
 */
class PageController
{
    /**
     * Render the homepage
     */
    public function index()
    {
        // Gather all data
        $data = [
            'lang' => current_lang(),
            'tools' => Tool::all(),
            'faqs' => Faq::all(),
            'faqCategories' => Faq::categories(),
            'testimonials' => Testimonial::all(),
            'services' => Service::all(),
            'gallery' => Gallery::all(),
            'visitorCount' => VisitorService::getCount(),
            'config' => [
                'name' => config('name'),
                'title' => config('title'),
                'description' => config('description'),
                'social' => config('social'),
                'email' => config('email'),
                'phone' => config('phone'),
                'whatsapp' => config('whatsapp'),
                'emailjs' => config('emailjs'),
            ],
        ];

        // Render the main layout
        echo View::render('layouts/main', $data);
    }
}
