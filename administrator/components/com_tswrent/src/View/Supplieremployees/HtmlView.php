<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\View\Supplieremployees;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View class for a list of supplieremployees.
 *
 * @since  __BUMP_VERSION__
 * 
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The search tools form
     *
     * @var    Form
     * @since  __BUMP_VERSION__
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var    array
     * @since  __BUMP_VERSION__
     */
    public $activeFilters;

    /**
     * An array of items
     *
     * @var    array
     * @since  __BUMP_VERSION__
     */
    protected $items;

    /**
     * The pagination object
     *
     * @var    Pagination
     * @since  __BUMP_VERSION__
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var    CMSObject
     * @since  __BUMP_VERSION__
     */
    protected $state;

    /**
     * Is this view an Empty State
     *
     * @var  boolean
     * @since __BUMP_VERSION__
     */
    private $isEmptyState = false;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @since   __BUMP_VERSION__
     *
     * @throws  Exception
     */
    public function display($tpl = null)
    {
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        if (!\count($this->items) && $this->isEmptyState = $this->get('IsEmptyState')) {
            $this->setLayout('emptystate');
        }

        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
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
     */
    protected function addToolbar(): void
    {
        $canDo   = ContentHelper::getActions('com_tswrent');
        $user    = Factory::getApplication()->getIdentity();
        $toolbar = Toolbar::getInstance();

        ToolbarHelper::title(Text::_('COM_TSWRENT_MANAGER_SUPPLIEREMPLOYEES'), 'bookmark supplieremployees');

        if ($canDo->get('core.create') || \count($user->getAuthorisedCategories('com_tswrent', 'core.create')) > 0)  {
            $toolbar->addNew('supplieremployee.add');
        }

        if (!$this->isEmptyState && ($canDo->get('core.edit.state') || ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')))) {
            /** @var  DropdownButton $dropdown */
            $dropdown = $toolbar->dropdownButton('status-group', 'JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            if ($canDo->get('core.edit.state')) {
                if ($this->state->get('filter.published') != 2) {
                    $childBar->publish('supplieremployees.publish')->listCheck(true);

                    $childBar->unpublish('supplieremployees.unpublish')->listCheck(true);
                }

                if ($this->state->get('filter.published') != -1) {
                    if ($this->state->get('filter.published') != 2) {
                        $childBar->archive('supplieremployees.archive')->listCheck(true);
                    } elseif ($this->state->get('filter.published') == 2) {
                        $childBar->publish('publish')->task('supplieremployees.publish')->listCheck(true);
                    }
                }

                $childBar->checkin('supplieremployees.checkin');

                if ($this->state->get('filter.published') != -2) {
                    $childBar->trash('supplieremployees.trash')->listCheck(true);
                }
            }

            if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
                $toolbar->delete('supplieremployees.delete', 'JTOOLBAR_EMPTY_TRASH')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }

            // Add a batch button
            if (
                $user->authorise('core.create', 'com_tswrent')
                && $user->authorise('core.edit', 'com_tswrent')
                && $user->authorise('core.edit.state', 'com_tswrent')

            ) {
                $childBar->popupButton('batch', 'JTOOLBAR_BATCH')
                    ->selector('collapseModal')
                    ->listCheck(true);
            }
        }

        if ($user->authorise('core.admin', 'com_tswrent') || $user->authorise('core.options', 'com_tswrent')){
            $toolbar->preferences('com_tswrent');
        }

        $toolbar->help('Tswrent');
    }
}

