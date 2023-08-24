<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Model;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use TSWEB\Component\Tswrent\Administrator\Helper\ContactHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Contact model.
 *
 * @since  __BUMP_VERSION__
 * 
 */
class ContactModel extends AdminModel
{

    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * 
     * @since  __BUMP_VERSION__
     * 
     */
    protected $text_prefix = 'COM_TSWRENT_CONTACT';

    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  __BUMP_VERSION__
     * 
     */
    public $typeAlias = 'com_tswrent.contact';
    /**
     * Batch copy/move command. If set to false, the batch copy/move command is not supported
     *
     * @var  string
     * 
     *  @since   __BUMP_VERSION__
     */
    protected $batch_copymove = 'category_id';

    /**
     * Allowed batch commands
     *
     * @var  array
     * 
     *  @since   __BUMP_VERSION__
     * 
     */
    protected $batch_commands = [
        'brand_id'   => 'batchBrand',
    ];

 /**
     * Batch protuct changes for a group of contacts.
     *
     * @param   string  $value     The new value matching a client.
     * @param   array   $pks       An array of row IDs.
     * @param   array   $contexts  An array of item contexts.
     *
     * @return  boolean  True if successful, false otherwise and internal error is set.
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    protected function batchBrand($value, $pks, $contexts)
    {
        // Set the variables
        $user = $this->getCurrentUser();

        /** @var \TSWEB\Component\Tswrent\Administrator\Table\ContactTable $table */
        $table = $this->getTable();

        foreach ($pks as $pk) {
            if (!$user->authorise('core.edit', $contexts[$pk])) {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

                return false;
            }

            $table->reset();
            $table->load($pk);
            $table->cid = (int) $value;

            if (!$table->store()) {
                $this->setError($table->getError());

                return false;
            }
        }

        // Clean the cache
        $this->cleanCache();

        return true;
    }


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
        if (empty($record->id) || $record->published != -2) {
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
        $form = $this->loadForm('com_tswrent.contact', 'contact', ['control' => 'jform', 'load_data' => $loadData]);

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
        $data = $app->getUserState('com_tswrent.edit.contact.data', []);

        if (empty($data)) {
            $data = $this->getItem();

        }
        $this->preprocessData('com_tswrent.contact', $data);

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
            $item->supplier_ids = ContactHelper::getInputSupplierRelation($id);
            $item->customer_ids = ContactHelper::getInputCustomerRelation($id);
            $item->tswrentemployee = ContactHelper::getInputTswrentemployeeRelation($id);
        }
        return $item;
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param   Table  $table  A record object.
     *
     * @return  array  An array of conditions to add to ordering queries.
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    protected function getReorderConditions($table)
    {
        $condition   = array();
		$condition[] = 'catid = ' . (int) $table->catid;
        return $condition;
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
            //Save Contact/Supplier relation 
            if(!empty($data['supplier_ids'])){
                $supplier_ids=$data['supplier_ids'];
                foreach( $supplier_ids as $k => $v)
                { 
                    $supplier_id[]= $v['supplier_id'];
                }
                //clear empty and 0 item
                if(!empty($supplier_id)){
                    $supplier_id = array_diff($supplier_id, array(0,''));
                }
                if(!empty($supplier_id))
                {
                    $supplier_id= array_unique($supplier_id);
                
                    ContactHelper::saveContactRelation($id,$supplier_id,'contactsupplier');
                }else{
                    ContactHelper::deleteContactRelation($id,'contactsupplier');
                }
            } else{ContactHelper::deleteContactRelation($id,'contactsupplier');}  

            //Save Contact/Customer relation 
            if(!empty($data['customer_ids'])){
                $customer_ids=$data['customer_ids'];
                foreach( $customer_ids as $k => $v)
                { 
                    $customer_id[]= $v['customer_id'];
                }
                //clear empty and 0 item
                if(!empty($customer_id)){
                    $customer_id = array_diff($customer_id, array(0,''));
                }
                if(!empty($customer_id))
                {
                    $customer_id= array_unique($customer_id);
                
                    ContactHelper::saveContactRelation($id,$customer_id,'contactcustomer');
                }else{
                    ContactHelper::deleteContactRelation($id,'contactcustomer');
                }
            }else{ContactHelper::deleteContactRelation($id,'contactcustomer');}
                        
            //Save Contact/Customer relation 
            if(!empty($data['tswrentemployee'])){
                $customer_id=['1'];               
                ContactHelper::saveContactRelation($id,$customer_id,'tswrent');
            }else{
                ContactHelper::deleteContactRelation($id,'tswrent');
            }
        

        return parent::save($data);
    }
}