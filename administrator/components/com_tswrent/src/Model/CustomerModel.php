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
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use TSWEB\Component\Tswrent\Administrator\Helper\ContactHelper;


// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Customer model.
 *
 * @since  __BUMP_VERSION__
 * 
 */
class CustomerModel extends AdminModel
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * 
     * @since  __BUMP_VERSION__
     * 
     */
    protected $text_prefix = 'COM_TSWRENT_CUSTOMER';
   
    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  __BUMP_VERSION__
     * 
     */
    public $typeAlias = 'com_tswrent.customer';


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
        $form = $this->loadForm('com_tswrent.customer', 'customer', ['control' => 'jform', 'load_data' => $loadData]);

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
            $item->contact_ids = ContactHelper::getInputContactRelation($id,"customercontact");

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
        $data = $app->getUserState('com_tswrent.edit.customer.data', []);

        if (empty($data)) {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState('customer.id') == 0) {
                $filters     = (array) $app->getUserState('com_tswrent.Customers.filter');
                $filterCatId = $filters['category_id'] ?? null;

                $data->set('catid', $app->getInput()->getInt('catid', $filterCatId));
            }
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
		$user = Factory::getApplication()->getIdentity();

		$table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias = ApplicationHelper::stringURLSafe($table->alias);

		if (empty($table->alias))
		{
			$table->alias = ApplicationHelper::stringURLSafe($table->title);
		}

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
        if (parent::save($data))
        {
            $id=$data['id'];
            //Save Contact/Customer relation 
            if(!empty($data['contact_ids'])){
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
                
                    ContactHelper::saveContactRelation($id,$customer_id,'customer');
                }else{
                    ContactHelper::deleteContactRelation($id,'customer');
                }
            }else{ContactHelper::deleteContactRelation($id,'customer');}

        }
        return parent::save($data);
    }
}
