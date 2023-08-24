<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use TSWEB\Component\Tswrent\Administrator\Helper\TswrentHelper;
use TSWEB\Component\Tswrent\Administrator\Helper\ContactHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Supplier model.
 *
 * @since  __BUMP_VERSION__
 * 
 */
class SupplierModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  __BUMP_VERSION__
     * 
     */
    public $typeAlias = 'com_tswrent.supplier';



    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     *
     * @since   __BUMP_VERSION__
     * 
     */
    protected function canDelete($record)
    {
        if (empty($record->id) || $record->published != -2) {
            return false;
        }

        if (!empty($record->catid)) {
            return $this->getCurrentUser()->authorise('core.delete', 'com_tswrent.category.' . (int) $record->catid);
        }

        return parent::canDelete($record);
    }

    /**
     * A method to preprocess generating a new title in order to allow tables with alternative names
     * for alias and title to use the batch move and copy methods
     *
     * @param   integer  $categoryId  The target category id
     * @param   Table    $table       The JTable within which move or copy is taking place
     *
     * @return  void
     *
     * @since   __BUMP_VERSION__
     * 
     */
    public function generateTitle($categoryId, $table)
    {
        // Alter the title & alias
        $data         = $this->generateNewTitle($categoryId, $table->alias, $table->title);
        $table->title  = $data['0'];
        $table->alias = $data['1'];
    }
    /**
     * Method to test whether a record can have its state changed.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to change the state of the record.
     *                   Defaults to the permission set in the component.
     *
     * @since   __BUMP_VERSION__
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
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  \Joomla\CMS\Form\Form|boolean  A Form object on success, false on failure
     *
     * @since   __BUMP_VERSION__
     * 
    */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_tswrent.supplier', 'supplier', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }
        return $form;
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
            $item->brand_ids =      TswrentHelper::getInputSupplierBrandRelation($id,'supplierbrand');
            $item->contact_ids =    ContactHelper::getInputContactRelation($id, 'suppliercontact');
        }

        return $item;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   __BUMP_VERSION__
     * 
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app  = Factory::getApplication();
        $data = $app->getUserState('com_tswrent.edit.supplier.data', []);

        if (empty($data)) {
            $data = $this->getItem();

        }
        
        return $data;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param   Table  $table  A Table object.
     *
     * @return  void
     *
     * @since   __BUMP_VERSION__
     * 
    */
    protected function prepareTable($table)
    {
        $date = Factory::getDate();
        $user = $this->getCurrentUser();

        if (empty($table->id)) {
            // Set the values
            $table->created    = $date->toSql();
            $table->created_by = $user->id;

        } else {
            // Set the values
            $table->modified    = $date->toSql();
            $table->modified_by = $user->id;
        }

        // Increment the content version number.
        $table->version++;
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
            $id=$data['id'];
            
            $related_ids=$data['brand_ids'];
            foreach( $related_ids as $k => $v)
            {
                $related_id[]= $v['brand_id'];
            }
            //clear empty and 0 item
            if(!empty($related_id)){
                $related_id = array_diff($related_id, array(0));
            }
            if(!empty($related_id))
            {
                $related_id= array_unique($related_id);
                
                TswrentHelper::saveSupplierBrandRelation($id,$related_id,'supplierbrand');
            }else{
                TswrentHelper::deleteSupplierBrandRelation($id,$related_id,'supplierbrand');
            }
            //Save Contact/Customer relation 
            if(!empty($data['contact_ids'])){
                $contact_ids=$data['contact_ids'];
                foreach( $contact_ids as $k => $v)
                { 
                    $contact_id[]= $v['contact_id'];
                }
                //clear empty and 0 item
                if(!empty($contact_id)){
                    $contact_id = array_diff($contact_id, array(0,''));
                }
                if(!empty($contact_id))
                {
                    $contact_id= array_unique($contact_id);
                
                    ContactHelper::saveContactRelation($id,$contact_id,'suppliercontact');
                }else{
                    ContactHelper::deleteContactRelation($id,'suppliercontact');
                }
            }else{ContactHelper::deleteContactRelation($id,'suppliercontact');}

         return parent::save($data);
    }
        /**
     * Method to remove brand from Supplier.
     *
     * @param   integer $id  Supplier ID.
     * 
     * @param   integer $brand_id  Brand ID to remove.
     *
     * @return  boolean  True on success.
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    public function removebrand($id,$brand_id)
    {
        $db   = Factory::getContainer()->get('DatabaseDriver');
			// get previous entries
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__tswrent_brand_supplier_relation'));
			$query->where('supplier_id = ' . $id);
			$query->where('brand_id =' .$brand_id);
			$db->setQuery($query);

            try {
                $db->execute();
            } catch (\RuntimeException $e) {
                $this->setError($e->getMessage());
    
                return false;
            }
        }

         /**
     * Method to remove brand from Supplier.
     *
     * @param   integer $id  Supplier ID.
     * 
     * @param   integer $contact_id  Contact ID to remove.
     *
     * @return  boolean  True on success.
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    public function removecontact($id,$contact_id)
    {
        $db   = Factory::getContainer()->get('DatabaseDriver');
			// get previous entries
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__tswrent_contact_relation'));
			$query->where('supplier_id = ' . $id);
			$query->where('contact_id =' .$contact_id);
			$db->setQuery($query);

            try {
                $db->execute();
            } catch (\RuntimeException $e) {
                $this->setError($e->getMessage());
    
                return false;
            }
        }
}
