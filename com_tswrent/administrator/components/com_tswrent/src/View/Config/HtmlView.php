<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\View\Config;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use TSWEB\Component\Tswrent\Administrator\Model\ConfigModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View class to edit a config.
 *
 * @since  __BUMP_VERSION__
 * 
 */
class HtmlView extends BaseHtmlView
{
		/**
	 * The \JForm object
	 *
	 * @var  \JForm
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var  object
	 */
	protected $item;
	
	/**
	 * The model state
	 *
	 * @var    object
	 */
	protected $state;

	/**
     * Object containing permissions for the item
     *
     * @var    CMSObject
     * 
     * @since  __BUMP_VERSION__
     */
    protected $canDo;
	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 * 
	 *  @since   __BUMP_VERSION__
	 * 
	 */
	public function display($tpl = null): void
	{
		/** @var ContactModel $model */
		$model       = $this->getModel();
		$this->form  = $model->getForm();
		$this->item  = $model->getItem();
		$this->state = $model->getState();
		$this->canDo    = ContentHelper::getActions('com_tswrent', 'config');

		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

        $this->addToolbar();


		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 * 
	 */
	protected function addToolbar()
	{

        $user       = $this->getCurrentUser();
		$toolbar    = Toolbar::getInstance();

		ToolbarHelper::title(
			Text::_('COM_TSWRENT_MANAGER_CONFIG_EDIT'), 
			'bookmark tswrent-configs');

  
        $toolbar->back('config.back', 'JTOOLBAR_BACK');

		if ($user->authorise('core.admin', 'com_tswrent') || $user->authorise('core.options', 'com_tswrent')){
            $toolbar->preferences('com_tswrent');
        }
        $toolbar->help('Banners:_New_or_Edit_Config');
	}
}
