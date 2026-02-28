<?php
/**
 * View – template renderer.
 *
 * Usage:
 *   View::render('movies/index', ['movies' => $movies]);
 *   View::render('movies/show',  ['movie'  => $movie], 'layouts/main');
 */

class View
{
    /**
     * Render a view template, optionally wrapped in a layout.
     *
     * @param string $template  Relative path inside /views, without .php extension.
     * @param array  $data      Variables to extract into the template scope.
     * @param string $layout    Layout template (relative to /views), or '' for none.
     */
    public static function render(
        string $template,
        array  $data   = [],
        string $layout = 'layouts/main'
    ): void {
        // Make data variables available in the template.
        extract($data, EXTR_SKIP);

        // Buffer the inner template first.
        $templateFile = VIEWS_PATH . '/' . $template . '.php';

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("View not found: {$templateFile}");
        }

        ob_start();
        require $templateFile;
        $content = ob_get_clean();   // $content is accessible inside the layout.

        // Wrap in layout if provided.
        if ($layout !== '') {
            $layoutFile = VIEWS_PATH . '/' . $layout . '.php';

            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout not found: {$layoutFile}");
            }

            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /**
     * Render a partial template snippet (no layout).
     *
     * @param string $partial  Relative path inside /views, without .php extension.
     * @param array  $data     Variables to extract.
     */
    public static function partial(string $partial, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $file = VIEWS_PATH . '/' . $partial . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("Partial not found: {$file}");
        }

        require $file;
    }

    /**
     * Escape output for safe HTML rendering.
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
