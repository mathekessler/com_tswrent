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
use TSWEB\Component\Tswrent\Administrator\Helper\OrderHelper;

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
            $item->days =       OrderHelper::countDays($item->startdate, $item->enddate);
            $item->hours =      OrderHelper::countHours($item->startdate, $item->enddate);
            $Factor=      $this->getgraduationFactor($item->graduation, $item->days);
            $item->factor=  $Factor["factor"];

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
        $input = Factory::getApplication()->getInput();

        // Alter the name for save as copy
        if ($input->get('task') == 'save2copy') {
            /** @var \Joomla\Component\Banners\Administrator\Table\BannerTable $origTable */
            $origTable = clone $this->getTable();
            $origTable->load($input->getInt('id'));

            if ($data['title'] == $origTable->title) {
                list($title, $alias) = $this->generateNewTitle( $data['alias'], $data['title']);
                $data['title']       = $title;
                $data['alias']      = $alias;
            } else {
                if ($data['alias'] == $origTable->alias) {
                    $data['alias'] = '';
                }
            }

            $data['state'] = 0;
        }


        return parent::save($data);
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
    public function c_contactid($id)
    {
      $db    = Factory::getDbo();
        $query = $db->getQuery(true)
        ->select(
            [
                $db->quoteName('a.id', 'value'),
                $db->quoteName('a.title', 'text'),
              
            ]
        )
        ->from($db->quoteName('#__tswrent_contacts', 'a'))
        ->join('LEFT',$db->quoteName('#__tswrent_contact_relation', 'b'), $db->quoteName('b.contact_id').'= '.$db->quoteName('a.id') )
        ->where($db->quoteName('b.customer_id') . ' ='.$id)
        ->where($db->quoteName('a.published') . ' = 1')
        ->order($db->quoteName('a.title'));
        $db->setQuery($query);
        
        try {
            $rows = $db->loadAssocList();

            } catch (\RuntimeException $e) {
                $this->setError($e->getMessage());
    
                return false;
            } 
            
        return ($rows);
           
    }

    public function getgraduationFactor($id,$days)
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
        ->select('a.graduations')
        ->from($db->quoteName('#__tswrent_graduations', 'a'))
        ->where($db->quoteName('a.id') . ' ='.$id);
        $db->setQuery($query);
        
        try {
            $rows = $db->loadResult();
            $row = json_decode($rows,true);

        } catch (\RuntimeException $e) {
           $this->setError($e->getMessage());
    
                return false;
        } 
        
        //echo print_r($rows);
        $searchValue=(int)$days;
        $foundGraduation = null;
        $key=true;

        while ($searchValue > 0 && !$foundGraduation) {
            $array= $row;
           //echo $searchValue;
            foreach ($array as $graduationKey => $graduationArray) {
               // echo $searchValue;
                //echo print_r($graduationArray["days"]);
                if ($graduationArray["days"] == $searchValue) 
                {
                    //print_r($foundGraduation);
                    $foundGraduation = $graduationArray;
                   
                    break;
                }
            }
            $searchValue--;
            //echo $searchValue;
        }
       
        return ($foundGraduation);
    }
   

}
