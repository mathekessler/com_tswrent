<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Controller;

use Joomla\CMS\Language\Text;
use \Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;
use \Joomla\CMS\Layout\LayoutHelper;
use TSWEB\Component\Tswrent\Administrator\Pdf\OrderDocument;


\defined('_JEXEC') or die;


/**
 * Order ontroller class 
 *
 * @since  __BUMP_VERSION__
 */
class OrderController extends FormController
{


    public function getcustomercontact()
    {   
        // Get the model.
		$model  = $this->getModel();
        $id = $this->input->get('id','int');
                  
        if (!$model->customercontactid($id)) {
            $this->app->enqueueMessage($model->getError(), 'warning');
        };
        
        echo new JsonResponse($model->customercontactid($id));        
    }


    public function getGraduationFactor()
    {   
       // Get the model.
		$model  = $this->getModel();

        // Sicheres Parsen der Input-Parameter 
		$id    = $this->input->getInt('id');
        $days    = $this->input->getInt('days');		

        // Aufruf der Methode im Model
        $result = $model->getGraduationFactor($id, $days);
        
        // Error.
        if ($result === false) {
            $this->app->enqueueMessage($model->getError(), 'warning');
        }

        echo new JsonResponse($result);
     }


     public function renderNewOrderProductRow() 
    {
        $app = Factory::getApplication();

        $id = $this->input->getInt('id');

        if (!$id) {
            echo new JsonResponse(null, 'Invalid product ID', true);
            return;
        }
        try {
           
            $product = $this->getModel()->getProductDataForOrderRow($id);

            if (!$product) {
                echo new JsonResponse(null, 'Product not found', true);
                return;
            }

            // Formulardaten für Kontext holen (Start-/Enddatum)
            $jform = $this->input->get('jform', [], 'array');
            $orderId = $jform['id'] ?? 0;
            // Den orderId-Wert sicher als Integer aus dem jform-Array holen, Standardwert 0, falls nicht vorhanden oder leer.
            $orderId = $this->input->getInt('jform.id', 0);

            // Defaultwerte setzen
            $product->productdiscount = 0;
            $product->reserved_quantity = 0;
            $product->product_price_total = 0;
        

            // Formular laden, um die Felder rendern zu können
            $form = $this->getModel()->getForm();

            // Verfügbaren Lagerbestand berechnen
            $availableStock = $this->getModel()->getAvailableStock($product->product_id, $jform['startdate'] ?? null, $jform['enddate'] ?? null, $orderId);

            // Render die Layout-Zeile
            $rowHtml = LayoutHelper::render('order.order_product_row', ['product' => $product, 'form' => $form, 'available_stock' => $availableStock]);

            echo new JsonResponse(['html' => $rowHtml]);
        } catch (\Exception $e) {
            echo new JsonResponse(null, $e->getMessage(), true);
        }

        $app->close(); // Wichtig, um sauberen JSON-Output zu garantieren
    }
    
    public function updateState(): void
    {
        $app = Factory::getApplication();
        $id = $this->input->getInt('id');
        $state = $this->input->getInt('orderstate');

        $model = $this->getModel('Order');
        $table = $model->getTable();

        if (!$table->load($id)) {
            echo new JsonResponse(null, 'Order not found', true);
            $app->close();
        }

        $table->orderstate = $state;
        $table->store();

        echo new JsonResponse(['id' => $id, 'orderstate' => $state]);
        $app->close();
    }

    /**
     * Task to generate a PDF based on the order state.
     *
     * @return void
     */
    public function generatePdf(): void
    {
        $app = Factory::getApplication();
        $id  = $this->input->getInt('id');

        // Erst prüfen: wurde der Datensatz bereits gespeichert (id > 0)?
        if ($id <= 0) {
            $app->enqueueMessage(Text::_('COM_TSWRENT_PDF_SAVE_BEFORE_GENERATE'), 'warning');
            // Zurück zur Bearbeitungsansicht (Neuanlage), damit Benutzer speichern kann
            $app->redirect(Route::_('index.php?option=com_tswrent&task=order.add', false));
            return;
        }

        // 1. Model und Bestelldaten laden
        $model = $this->getModel('Order');
        $order = $model->getItem($id);

        //$app->enqueueMessage(print_r( $order), 'warning');
        
        if (!$order || empty($order->id)) {
            // Falls das Item nicht existiert oder keine gültige ID hat,
            // dem Anwender sagen, dass er zuerst speichern muss.
            $app->enqueueMessage(Text::_('COM_TSWRENT_PDF_SAVE_BEFORE_GENERATE'), 'warning');
            $app->redirect(Route::_('index.php?option=com_tswrent&view=orders', false));
            return;
        }
        
        // Get order state text
        $orderstates = [
            0 => 'Offer',
            1 => 'OrderConfirmation',
            2 => 'Invoice'
        ];
        $order->orderstate_text = $orderstates[$order->orderstate] ?? 'Unknown';

        // 2. Logik basierend auf dem orderstate
        $documentType = '';
        switch ($order->orderstate) {

            case 0: // z.B. 'Angebot'
                $documentType = 'Offer';
                break;
            case 1: // z.B. 'Bestellt'
                $documentType = 'OrderConfirmation';
                break;
            case 2: // z.B. 'Rechnung'
                $documentType = 'Invoice';
                break;
            default:
                $app->enqueueMessage(Text::_('COM_TSWRENT_ERROR_PDF_NO_DOCUMENT_FOR_STATE'), 'warning');
                $app->redirect(Route::_('index.php?option=com_tswrent&view=orders', false));
                return;
        }

        $pdf = new OrderDocument(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Generate and output the PDF
        $pdf->generate($order, $documentType);
        
        $app->close();
    }

}
