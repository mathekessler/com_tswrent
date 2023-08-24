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
use TSWEB\Component\Tswrent\Administrator\Helper\ConfigHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Config model.
 *
 * @since  __BUMP_VERSION__
 * 
 */
class ConfigModel extends AdminModel
{

    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * 
     * @since  __BUMP_VERSION__
     * 
     */
    protected $text_prefix = 'COM_TSWRENT_CONFIG';

    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  __BUMP_VERSION__
     * 
     */
    public $typeAlias = 'com_tswrent.config';


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
       
    }

    /**
     * Allows preprocessing of the Form object.
     *
     * @param   Form    $form   The form object
     * @param   array   $data   The data to be merged into the form object
     * @param   string  $group  The plugin group to be executed
     *
     * @return  void
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
        if ($this->canCreateCategory()) {
            $form->setFieldAttribute('catid', 'allowAdd', 'true');

            // Add a prefix for categories created on the fly.
            $form->setFieldAttribute('catid', 'customPrefix', '#new#');
        }

        parent::preprocessForm($form, $data, $group);
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

        if (parent::save($data))
        {
            //Save Config/Supplier relation 
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
                
                    ConfigHelper::saveConfigRelation($id,$supplier_id,'supplier');
                }else{
                    ConfigHelper::deleteConfigRelation($id,'supplier');
                }
            } else{ConfigHelper::deleteConfigRelation($id,'supplier');}  

            //Save Config/Customer relation 
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
                
                    ConfigHelper::saveConfigRelation($id,$customer_id,'customer');
                }else{
                    ConfigHelper::deleteConfigRelation($id,'customer');
                }
            }else{ConfigHelper::deleteConfigRelation($id,'customer');}
        }

        return parent::save($data);
    }

    /**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $category_id  The id of the parent.
	 * @param   string   $alias        The alias.
	 * @param   string   $title         The title.
	 *
	 * @return  array  Contains the modified title and alias.
	 *
	 * @since   3.1
	 */
	protected function generateNewTitle($category_id, $alias, $title)
	{
		// Alter the title & alias
		$table = $this->getTable();

		while ($table->load(array('alias' => $alias, 'catid' => $category_id)))
		{
			if ($title == $table->title)
			{
				$title = StringHelper::increment($title);
			}

			$alias = StringHelper::increment($alias, 'dash');
		}

		return array($title, $alias);
	}

    /**
     * Is the user allowed to create an on the fly category?
     *
     * @return  boolean
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    private function canCreateCategory()
    {
        return Factory::getApplication()->getIdentity()->authorise('core.create', 'com_tswrent');
    }
}
