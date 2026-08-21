<?php
/**
 * View — Template Renderer
 */
class View
{
    /**
     * Render a view with data
     *
     * @param string $view  View path relative to views/ (e.g. 'layouts/main')
     * @param array  $data  Variables to extract into the view
     * @return string       Rendered HTML
     */
    public static function render($view, $data = [])
    {
        $file = VIEWS_PATH . '/' . $view . '.php';

        if (!file_exists($file)) {
            throw new RuntimeException("View not found: {$view}");
        }

        // Extract data as variables
        extract($data);

        // Start output buffering
        ob_start();
        require $file;
        return ob_get_clean();
    }

    /**
     * Render a section partial inside a layout
     *
     * @param string $section  Section name (e.g. 'hero', 'about')
     * @param array  $data     Variables to pass
     * @return string          Rendered HTML
     */
    public static function section($section, $data = [])
    {
        return self::render('sections/' . $section, $data);
    }

    /**
     * Render a partial
     *
     * @param string $partial  Partial name (e.g. 'navbar', 'footer')
     * @param array  $data     Variables to pass
     * @return string          Rendered HTML
     */
    public static function partial($partial, $data = [])
    {
        return self::render('partials/' . $partial, $data);
    }
}
