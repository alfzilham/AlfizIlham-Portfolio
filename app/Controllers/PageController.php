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
        $dynamic = self::localizedContent();
        $faqs = Faq::all();
        $services = Service::all();
        $testimonials = Testimonial::all();
        $gallery = Gallery::all();
        self::localizeRows($faqs, 'faq', $dynamic);
        self::localizeRows($services, 'service', $dynamic);
        self::localizeRows($testimonials, 'testimonial', $dynamic);
        self::localizeRows($gallery, 'gallery', $dynamic);
        // Gather all data
        $data = [
            'lang' => current_lang(),
            'projects' => Project::all(),
            'showcase' => ShowcaseProject::all(),
            'isAdmin' => !empty($_SESSION['is_admin']),
            'tools' => Tool::all(),
            'faqs' => $faqs,
            'faqCategories' => Faq::categories(),
            'testimonials' => $testimonials,
            'services' => $services,
            'gallery' => $gallery,
            'certificates' => Certificate::all(),
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

    private static function localizedContent()
    {
        $content = i18n::t('dynamic_content');
        return is_array($content) ? $content : [];
    }

    private static function localizeRows(array &$rows, $type, array $dynamic)
    {
        foreach ($rows as &$row) {
            $contentKey = (int) ($row['sort_order'] ?? $row['id']);
            if (isset($dynamic[$type][$contentKey])) $row = array_merge($row, $dynamic[$type][$contentKey]);
        }
        unset($row);
    }
}
