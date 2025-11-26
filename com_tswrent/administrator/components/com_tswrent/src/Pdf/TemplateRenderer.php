<?php

namespace TSWEB\Component\Tswrent\Administrator\pdf;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * Minimal template renderer for component PDF templates.
 *
 * Uses plain PHP templates located in `tmpl/pdf/{name}.php`.
 * This keeps HTML out of the PDF generator class and is simple to use
 * without introducing extra Joomla layout dependencies.
 */
class TemplateRenderer
{
    /**
     * Render a template and return the HTML string.
     *
     * @param string $name Template name (without .php)
     * @param array  $data Variables to be extracted into the template
     *
     * @return string
     * @throws \RuntimeException
     */
    public function render(string $name, array $data = []): string
    {
        $basePath = JPATH_ADMINISTRATOR . '/components/com_tswrent/layouts/order/pdf/';
        $file = $basePath . $name . '.php';

        if (!is_file($file)) {
            throw new \RuntimeException('PDF template not found: ' . $file);
        }

        // Make translations available in the template
        $data['Text'] = '\Joomla\\CMS\\Language\\Text';

        extract($data, EXTR_SKIP);

        ob_start();
        include $file;
        return (string) ob_get_clean();
    }
}
