<?php

namespace TSWEB\Component\Tswrent\Administrator\Pdf;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Component\ComponentHelper;
use TSWEB\Component\Tswrent\Administrator\Pdf\TemplateRenderer;

// Ensure TCPDF is available. Prefer component Composer autoload if present.
if (!class_exists('\TCPDF')) {
    $vendorAutoload = JPATH_ADMINISTRATOR . '/components/com_tswrent/vendor/autoload.php';
    $tcpdfFile = JPATH_ADMINISTRATOR . '/components/com_tswrent/vendor/tcpdf/tcpdf.php';

    if (is_file($vendorAutoload)) {
        require_once $vendorAutoload;
    } elseif (is_file($tcpdfFile)) {
        require_once $tcpdfFile;
    } else {
        throw new \RuntimeException('TCPDF not found in component vendor directory.');
    }
}

/**
 * Custom PDF class for Order Documents.
 */
class OrderDocument extends \TCPDF
{
    /**
     * @var object The order data object.
     */
    public $order;

    /** @var object|null The customer data object. */
    public $customer_detail;

    /** @var \\Joomla\\Registry\\Registry|object|null The component params. */
    public $params;

    /**
     * Overrides the default Header method.
     */
    public function Header(): void
    {
        // Block 1: Logo
        $logoPath = $this->params?->get('company_logo');
        if ($logoPath) {
            // The media field might add extra info after a #. We only need the path.
            $logoParts = explode('#', $logoPath);
            $cleanLogoPath = $logoParts[0];
            $this->Image(JPATH_ROOT . '/' . $cleanLogoPath, 15, 10, 50, 0, '', '', 'T', false, 300, 'L', false, false, 0, false, false, false);
        }

        // Block 2: Adressblock Empfänger
        // Absenderzeile über dem Adressfeld
        $this->SetFont('helvetica', '', 6);

        $companyTitle = $this->params?->get('company_title', '') ?? '';
        $companyAddress = $this->params?->get('company_address', '') ?? '';
        $companyPostal = $this->params?->get('company_postalcode', '') ?? '';
        $companyCity = $this->params?->get('company_city', '') ?? '';

        $contactPrename = ($this->order->contact_detail->prename ??'');
        $contactName = ($this->order->contact_detail->name ?? '');
        $senderLine = trim($companyTitle . "\n" . $contactPrename ." " . $contactName. "\n" . $companyAddress . "\n" . $companyPostal . ' ' . $companyCity);
        
        $pageWidth = $this->getPageWidth();
        $usableWidth = $pageWidth - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT;
        $block2Width = 100; // Breite des Adressblocks oben rechts
        $positionBlock2 = PDF_MARGIN_LEFT+($usableWidth - $block2Width); // Position des Adressblocks oben rechts
        $this->SetXY($positionBlock2, 10);
        $this->MultiCell($block2Width, 15, $senderLine, 0, 'R', 0, 0, '', '', true);
        $this->Ln(2);

    }

    /**
     * Overrides the default Footer method.
     */
    public function Footer(): void
    {
        // Position 1.5 cm von unten
        $this->SetY(-15);
        $this->SetFont('helvetica', '', 8);
        // Linie über dem Footer
        $this->SetLineStyle(['width' => 0.2, 'color' => [150, 150, 150]]);
        $this->Cell(0, 3, '', 'T', 1, 'C');

        // Bankverbindung (links) und Seitenzahl (rechts)
        $footerCol1 =   $this->params->get('company_bankname', '') . " | " .
                $this->params->get('company_bankaccount_owner', '') . " | " .
                $this->params->get('company_iban', '');

        // Page number (localized), sanitize to remove NBSPs and normalize whitespace
        $pageNumber = Text::sprintf('JLIB_HTML_PAGE_CURRENT_OF_TOTAL', $this->getAliasNumPage(), $this->getAliasNbPages());

        // Positioniere die beiden Spalten innerhalb der nutzbaren Seitenbreite
        $this->SetY(-12); // etwas über dem unteren Seitenrand
        $pageWidth = $this->getPageWidth();
        $usableWidth = $pageWidth - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT;

        // Linke Spalte: etwa 2/3 der nutzbaren Breite, linksbündig
        $leftWidth = $usableWidth * 0.66; 
        $this->MultiCell($leftWidth, 5, $footerCol1, 0,  'L',0, 0, 25, '', true);

        // Rechte Zelle – verbleibende Breite, rechtsbündig und bis zum rechten Rand
        $this->MultiCell(0, 5, $pageNumber, 0, 'R', 0, 0, '','', true);

        // Optional: falls später andere Bereiche wieder Padding benötigen, könnte man
        // hier alte Padding-Werte wiederherstellen. Für Footer reicht 0.
    }
    /**
     * Generates the PDF document.
     *
     * @param   object  $order         The order data. 
     * @param   string  $documentType  The type of the document (e.g., 'Offer', 'Invoice').
     * @param   bool    $saveToFile    Whether to save the PDF to a file instead of outputting. Default: false
     *
     * @return  string|void  The file path if $saveToFile is true, void otherwise.
     */
    public function generate(object $order, string $documentType, bool $saveToFile = false)
    {
        // Daten an die PDF-Klasse übergeben
        $this->order = $order;
        $this->customer_detail = $order->customer_detail ?? null;
        $this->params = ComponentHelper::getParams('com_tswrent');

        // Metadaten setzen
        $this->SetTitle(Text::_('COM_TSWRENT_TITLE') . ' ' . ($order->title ?? ''));
        $this->SetSubject(Text::_('COM_TSWRENT_SUBJECT') . ' ' . $documentType);

        // Standard-Setup
        $this->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT); // Erhöhter oberer Rand für den Header
        $this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $this->AddPage();

        // Set font
        // Use a Unicode-capable font if available to support UTF-8 (Umlaute etc.).
        // Falls `dejavusans` nicht vorhanden ist, fällt TCPDF auf `helvetica` zurück.
        try {
            $this->SetFont('dejavusans', '', 9);
        } catch (\Exception $e) {
            $this->SetFont('helvetica', '', 8);
        }

        // Empfängeradresse (wenn vorhanden)
        if (!empty($this->customer_detail)) {
            $this->SetFont('', 'B', 11);
            $customerAddress = (
                ($this->customer_detail->title ?? '') . "\n" .
                ($this->customer_detail->address ?? '') . "\n" .
                (($this->customer_detail->postalcode ?? '') . ' ' . ($this->customer_detail->city ?? ''))
            );
            $this->MultiCell(0, 5, $customerAddress, 0, 'R', 0, 1, 10, 40, true);
        }

        // Betreffzeile
        $this->SetY(55); // Start-Y-Position
        $this->SetFont('helvetica', 'B', 14);
        $subject = Text::_(strtoupper($this->order->orderstate_text ?? '')) . ' Nr. ' . ($this->order->id ?? '');
        $this->Cell(0, 10, $subject, 0, 1, 'L');
        $this->Ln(5);
        
        // Project Title
        //$this->SetY(55); // Start-Y-Position
        $this->SetFont('helvetica', 'B', 14);
        $subject = $this->order->title;
        $this->Cell(0, 10, $subject, 0, 1, 'L');
        $this->Ln(5);
        
        // Rechnungs-/Angebotsdaten
        $this->SetFont('helvetica', '', 10);
        //$dataY = 85; // Start-Y-Position

        // Get the date format from the language file to support localization.
        $dateFormat = Text::_('COM_TSWRENT_PDF_DATETIME_FORMAT');

        $startDateFormatted = '';
        if (!empty($this->order->startdate)) {
            $startDateFormatted = (new Date($this->order->startdate))->format($dateFormat);
        }
        $endDateFormatted = '';
        if (!empty($this->order->enddate)) {
            $endDateFormatted = (new Date($this->order->enddate))->format($dateFormat);
        }

        $documentData = [
            Text::_('COM_TSWRENT_PDF_CONTACT') => trim((string) ($this->order->contact_detail->prename ?? '') . ' ' . ($this->order->contact_detail->name ?? '')),
            Text::_('COM_TSWRENT_PDF_CUSTOMER_CONTACT') => trim((string) ($this->order->c_contact_detail->prename ?? '') . ' ' . ($this->order->c_contact_detail->name ?? '')),
            Text::_('COM_TSWRENT_PDF_PROJECT_ADDRESS') => trim((($this->order->venue_name ?? '') . ' - ' . ($this->order->address ?? '') . ' - ' . ($this->order->postalcode ?? '') . ' ' . ($this->order->city ?? ''))),
            Text::_('COM_TSWRENT_PDF_PROJECT_DATE') => ($startDateFormatted . ' - ' . $endDateFormatted),
        ];

        foreach ($documentData as $label => $value) {
            $this->Cell(30, 5, $label . ':', 0, 0, 'L');
            $this->Cell(45, 5, $value, 0, 1, 'L');
        }
        $this->Ln(5);

        // Product-table and summary are provided by a template to keep this class clean.
        try {
            $renderer = new TemplateRenderer();
            $html = $renderer->render('order', ['order' => $this->order, 'params' => $this->params]);
            $this->writeHTML($html, true, false, true, false, '');
        } catch (\RuntimeException $e) {
            // Wenn Template fehlt oder Fehler beim Rendern, geben wir eine einfache Fehlermeldung aus.
            $this->writeHTML('<p>' . htmlspecialchars($e->getMessage()) . '</p>', true, false, true, false, '');
        }

        // Close and output PDF document
        $orderId = str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $filename = 'order_' . $orderId . '_' . $documentType . '.pdf';
        
        // Wenn $saveToFile true ist, wird die Datei auf dem Server gespeichert.
        if ($saveToFile) { // Controller hat entschieden: Speichern und Überschreiben
            // Speichere PDF-Datei im Verzeichnis /images/tswrent/Order_{ID}
            $uploadDir = JPATH_ROOT . '/images/tswrent/Order_'. $orderId ;
            
            // Erstelle Ordner, falls er nicht existiert
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filePath = $uploadDir . '/' . $filename;
            $this->Output($filePath, 'F');
            $this->Output($filename, 'I');

            return $filePath;
        } else {
            // Controller hat entschieden: Nur im Browser anzeigen, nicht speichern.
            $this->Output($filename, 'I');
        }
    }
}
