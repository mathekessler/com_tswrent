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
use Joomla\String\StringHelper;
use Joomla\Component\Categories\Administrator\Helper\CategoriesHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Graduation model.
 *
 * @since  __BUMP_VERSION__
 * 
 */
class GraduationModel extends AdminModel
{

    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * 
     * @since  __BUMP_VERSION__
     * 
     */
    protected $text_prefix = 'COM_TSWRENT_GRADUATION';

    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  __BUMP_VERSION__
     * 
     */
    public $typeAlias = 'com_tswrent.graduation';

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
        // Must be flagged as trashed.
        if (empty($record->id) || $record->state != -2) {
            return false;
        }
        /**
         * Spezialfall: Verhindern, dass der Eintrag mit der ID 1 gelöscht wird (z.B. Standard-Eintrag)
         */
        if ($record->id == 1) {
            $app = Factory::getApplication();    
            $app->enqueueMessage(Text::_('COM_TSWRENT_DELETE_DEFAULT_RECORD'), 'warning') ;

            return false;
        }
        // Check against the category.
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
        $form = $this->loadForm('com_tswrent.graduation', 'graduation', ['control' => 'jform', 'load_data' => $loadData]);

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
        $data = $app->getUserState('com_tswrent.edit.graduation.data', []);

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
     *  @since   __BUMP_VERSION__
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
        
        $this->validateday($data); // Überschneidungen prüfen
        
        // Alter the title and published state for Save as Copy
        if ($input->get('task') == 'save2copy') {
            $orig_table = clone $this->getTable();
            $orig_table->load((int) $input->getInt('id'));
            $data['published'] = 0;

            if ($data['title'] == $orig_table->title) {
                $data['title'] = StringHelper::increment($data['title']);
            }
        }
        
        return parent::save($data);
    }

    
    public function validateday($data)
    {
        $errors = [];

        if (!empty($data['graduations']) && is_array($data['graduations'])) {
            $intervals = [];
            foreach ($data['graduations'] as $key => $g) {
                $duration = (int) $g['duration'];
                $unit = (int) $g['unit']; // 1=day, 0=week
                $days = ($unit === 0) ? $duration * 7 : $duration;

                if ($days < 7) {
                    $start = $days;
                    $end = $days;
                } else {
                    $week = floor(($days - 7) / 7);
                    $start = 7 + $week * 7;
                    $end = $start + 6;
                }

                $intervals[$key] = ['start'=>$start, 'end'=>$end, 'key'=>$key];
            }

            foreach ($intervals as $i => $a) {
                foreach ($intervals as $j => $b) {
                    if ($i >= $j) continue;
                    if ($a['start'] <= $b['end'] && $a['end'] >= $b['start']) {
                        $errors[] = "Graduation Zeile {$a['key']} überlappt mit Zeile {$b['key']}.";
                    }
                }
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $msg) {
                $this->setError($msg);
            }
            return false; // Speichern abbrechen → zurück zum Formular
        }

        return true;
    }



}
