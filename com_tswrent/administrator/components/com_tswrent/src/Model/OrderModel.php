<?php 
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * @version     __BUMP_VERSION__
 */

namespace TSWEB\Component\Tswrent\Administrator\Model;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\String\StringHelper;
use Joomla\Database\ParameterType;
use TSWEB\Component\Tswrent\Administrator\Helper\OrderHelper;
use TSWEB\Component\Tswrent\Administrator\Helper\TswrentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Order model.
 *
 * @since  __BUMP_VERSION__
 * 
 */
class OrderModel extends AdminModel
{

    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * 
     * @since  __BUMP_VERSION__
     * 
     */
    protected $text_prefix = 'COM_TSWRENT_ORDER';

    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  __BUMP_VERSION__
     * 
     */
    public $typeAlias = 'com_tswrent.order';

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    protected function canDelete($record)
    {
        if (empty($record->id) || $record->state != -2) {
            return false;
        }

        if (!empty($record->catid)) {
            return $this->getCurrentUser()->authorise('core.delete', 'com_tswrent.category.' . (int) $record->catid);
        }

        return parent::canDelete($record);
    }

    /**
     * Method to test whether a record can have its state changed.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    protected function canEditState($record)
    {
        // Check against the category.
        if (!empty($record->catid)) {
            return $this->getCurrentUser()->authorise('core.edit.state', 'com_tswrent.category.' . (int) $record->catid);
        }

        // Default to component settings if category not known.
        return parent::canEditState($record);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form. [optional]
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not. [optional]
     *
     * @return  Form|boolean  A Form object on success, false on failure
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_tswrent.order', 'order', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        
        // Check the session for previously entered form data.
        $data = $app->getUserState('com_tswrent.edit.order.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }
        $this->preprocessData('com_tswrent.order', $data);

        return $data;
    }
    /**
     * Overloads the parent getItem() method.
     *
     * @param   integer  $pk  Primary key
     *
     * @return  object|boolean  Object on success, false on failure
     *
     * @since  __BUMP_VERSION__
     * @throws \Exception
     */

    
     public function getItem($pk = null){
        
        $item = parent::getItem($pk);
        
        if (!empty($item->id)) {
            $id = $item->id;
            $item->products =   OrderHelper::LoadOrderProducts($id);
            
            $db = $this->getDbo();
            // Load TSW Rent Contact data
            try {
                if (!empty($item->contact_id)) {
                    $query = $db->getQuery(true)
                        ->select('t_con.*')
                        ->from($db->quoteName('#__tswrent_contacts', 't_con'))
                        ->where($db->quoteName('t_con.id') . ' = :t_contact_id')
                        ->bind(':t_contact_id', $item->contact_id, ParameterType::INTEGER);

                    $db->setQuery($query);
                    $item->contact_detail = $db->loadObject();
                }
            } catch (\RuntimeException $e) {
                // Datenbankfehler abfangen und als Modell-Fehler speichern.
                $this->setError($e->getMessage());
                if (!isset($item->contact_detail)) {
                    $item->contact_detail = null;
                }
            }
            // Load Customer and contact data
            
            try {
                if (!empty($item->customer_id)) {
                    $query = $db->getQuery(true)
                        ->select('cus.*')
                        ->from($db->quoteName('#__tswrent_customers', 'cus'))
                        ->where($db->quoteName('cus.id') . ' = :customer_id')
                        ->bind(':customer_id', $item->customer_id, ParameterType::INTEGER);

                    $db->setQuery($query);
                    $item->customer_detail = $db->loadObject();
                }

                // NOTE: vorher war hier `if(!item->contact_id)` — das ist ein Bug.
                if (!empty($item->c_contact_id)) {
                    $query = $db->getQuery(true)
                        ->select('con.*')
                        ->from($db->quoteName('#__tswrent_contacts', 'con'))
                        ->where($db->quoteName('con.id') . ' = :c_contact_id')
                        ->bind(':c_contact_id', $item->c_contact_id, ParameterType::INTEGER);

                    $db->setQuery($query);
                    $item->c_contact_detail = $db->loadObject();
                }
            } catch (\RuntimeException $e) {
                // Datenbankfehler abfangen und als Modell-Fehler speichern.
                $this->setError($e->getMessage());
                // Stelle sicher, dass die Properties definiert sind, auch bei Fehlern.
                if (!isset($item->customer_detail)) {
                    $item->customer_detail = null;
                }
                if (!isset($item->contact_detail)) {
                    $item->c_contact_detail = null;
                }
            }

            foreach ($item->products as $product) {
                $item->available_stock = $this->getAvailableStock($product->product_id, $item->startdate, $item->enddate, $id);     
            }
            
          //$app = Factory::getApplication();    $app->enqueueMessage(print_r( $item->contact_detail), true) ;
        }    
        return $item;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param   Table  $table  A Table object.
     *
     * @return  void
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    protected function prepareTable($table)
    {
        $date = Factory::getDate();
        $user = $this->getCurrentUser();

		if (empty($table->id))
		{
            // Set the values
            $table->created    = $date->toSql();
            $table->created_by = $user->id;

        } else {
            // Set the values
            $table->modified    = $date->toSql();
            $table->modified_by = $user->id;
        }


    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success.
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    public function save($data)
    {
        $app = Factory::getApplication();
        $input = $app->getInput();
        $db = $this->getDbo();
 
        // Alter the title and published state for Save as Copy
        if ($input->get('task') === 'save2copy') {
            $orig_table = clone $this->getTable();
            $orig_table->load((int) $input->getInt('id'));
            $data['published'] = 0;
 
            if ($data['title'] === $orig_table->title) {
                $data['title'] = StringHelper::increment($data['title']);
            }
        }
 
        try {
            $db->transactionStart();
 
            // Save main record
            if (parent::save($data) === false) {
                throw new \Exception(Text::_('COM_TSWRENT_ORDER_SAVE_FAILED') . ' ' . $this->getError());
            }
 
            // Get the ID of the saved record
            $orderId = (int) $this->getState($this->getName() . '.id');
 
            $products = $input->get('jform', [], 'array')['products'] ?? [];
 
            // Save product associations using the helper
            if (!OrderHelper::saveOrderProducts($orderId, $products)) {
                throw new \Exception($this->getError() ?: 'Fehler beim Speichern der Produktzuordnungen.');
            }
 
            $db->transactionCommit();
            return true;
        } catch (\Exception $e) {
            $db->transactionRollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

     /**
     * Order --> Contacts for selecte customer.
     *
     * @param   integer $id  Customer ID.
     * 
     *
     * @return  array  Value and Text for Options.
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    public function customercontactid($id)
    {
    $id = (int) $id;

    if ($id <= 0) {
        return [];
    }

    $db = Factory::getDbo();

    $query = $db->getQuery(true)
        ->select([
            $db->quoteName('a.id', 'value'),
            $db->quoteName('a.title', 'text'),
        ])
        ->from($db->quoteName('#__tswrent_contacts', 'a'))
        ->join(
            'LEFT',
            $db->quoteName('#__tswrent_contact_relation', 'b') . ' ON ' . $db->quoteName('b.contact_id') . ' = ' . $db->quoteName('a.id')
        )
        ->where($db->quoteName('b.customer_id') . ' = ' . (int) $id) // 👈 Kein bind(), direkt eingebaut
        ->where($db->quoteName('a.state') . ' = 1')
        ->order($db->quoteName('a.title'));

    $db->setQuery($query);

    try {
        return $db->loadAssocList();
    } catch (\RuntimeException $e) {
        $this->setError($e->getMessage());
        return false;
    }
           
    }

    public function getGraduationFactor($id,$days)
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
        ->select('a.graduations')
        ->from($db->quoteName('#__tswrent_graduations', 'a'))
        ->where($db->quoteName('a.id') . ' ='.$id);
        $db->setQuery($query);
        
        try {
            $rows = $db->loadResult();
            $jsonGraduations = json_decode($rows,true);

            if (!$jsonGraduations) {
                $this->setError('No graduations data found.');
                return false;
        }

        $graduationsArray = $jsonGraduations;
        
        if (!is_array($graduationsArray)) {
            $this->setError('Invalid graduations JSON format.');
            return false;
        }

        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        } 
        
       
        $searchValue=(int)$days;
            $rows = $db->loadResult();
            $row = json_decode($rows,true);
        $foundGraduation = null;

        while ($searchValue > 0 && $foundGraduation === null) {
            foreach ($graduationsArray as $graduation) {
                if ((int) $graduation['days'] === $searchValue) {
                    $foundGraduation = $graduation;
                    break;
                }
            }
            $searchValue++;
        }
        return ($foundGraduation['factor'] );
    }
   

    /**
     * Berechnet den verfügbaren Lagerbestand für ein Produkt in einem bestimmten Zeitraum.
     *
     * @param   int     $productId  Die ID des Produkts.
     * @param   string  $startDate  Das Startdatum des Auftrags.
     * @param   string  $endDate    Das Enddatum des Auftrags.
     * @param   int     $orderId    Die ID des aktuellen Auftrags (um ihn bei der Berechnung auszuschließen).
     *
     * @return  int  Der verfügbare Lagerbestand.
     */
    public function getAvailableStock(int $productId, ?string $startDate, ?string $endDate, int $orderId = 0): int
    {
        if (!$productId || !$startDate || !$endDate) {
            return 0;
        }

        $db = $this->getDbo();

        // 1. Gesamtbestand des Produkts holen
        $query = $db->getQuery(true)
            ->select($db->quoteName('stock'))
            ->from($db->quoteName('#__tswrent_products'))
            ->where($db->quoteName('id') . ' = :productId')
            ->bind(':productId', $productId, \Joomla\Database\ParameterType::INTEGER);
        $db->setQuery($query);
        $totalStock = (int) $db->loadResult();

        // 2. Bereits reservierte Menge in diesem Zeitraum für ANDERE Aufträge berechnen
        $reservedStock = 0;
        if ($startDate && $endDate) {
            $query = $db->getQuery(true)
                ->select('SUM(op.reserved)')
                ->from($db->quoteName('#__tswrent_order_product', 'op'))
                ->join('INNER', $db->quoteName('#__tswrent_orders', 'o') . ' ON op.order_id = o.id')
                ->where($db->quoteName('op.product_id') . ' = :productId')
                ->where($db->quoteName('o.id') . ' != :orderId') // Schließe den aktuellen Auftrag aus
                ->where(
                    '(' . $db->quoteName('o.startdate') . ' < :endDate AND '
                    . $db->quoteName('o.enddate') . ' > :startDate)'
                )
                ->bind(':productId', $productId, ParameterType::INTEGER)
                ->bind(':orderId', $orderId, ParameterType::INTEGER)
                ->bind(':startDate', $startDate)
                ->bind(':endDate', $endDate);

            $db->setQuery($query);
            $reservedStock = (int) $db->loadResult();
        }

        // 3. Verfügbaren Bestand zurückgeben
        return $totalStock - $reservedStock;
    }

    /**
     * Holt die Basisdaten eines Produkts für eine neue Auftragszeile.
     *
     * @param   int  $productId  Die ID des Produkts.
     *
     * @return  ?object  Das Produkt-Objekt oder null, wenn nicht gefunden.
     * @throws  \Exception bei Datenbankfehlern.
     */
    public function getProductDataForOrderRow(int $productId): ?object
    {
        if (!$productId) {
            return null;
        }

        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select(
                'a.id AS product_id, 
                a.title AS product_title, 
                a.description AS product_description,
                a.price AS product_price'
            )
            ->from($db->quoteName('#__tswrent_products', 'a'))
            ->where($db->quoteName('a.id') . ' = :product_id')
            ->bind(':product_id', $productId);

        $db->setQuery($query);

        return $db->loadObject();
    }
}