<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   (C) 2008 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\View\Products;


use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Button\DropdownButton;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View class for a list of products.
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
    /**
     * An array of items
     *
     * @var    array
     * @since  1.6
     */
    protected $items;

    /**
     * The pagination object
     *
     * @var    Pagination
     * @since  1.6
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var    Registry
     * @since  1.6
     */
    protected $state;
    
    /**
     * The search tools form
     *
     * @var    Form
     * @since  1.6
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var    array
     * @since  1.6
     */
     public $activeFilters;
    
     /**
     * Is this view an Empty State
     *
     * @var  boolean
     * @since 4.0.0
     */
    private $isEmptyState = false;

    /**
     * Method to display the view.
     *
     * @param   string  $tpl  A template file to load. [optional]
     *
     * @return  void
     *
     * @since   1.6
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
        if (\count($errors = $this->get('Errors')) || $this->transitions === false) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
        } else {
            }


        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolbar(): void
    {
        $canDo   = ContentHelper::getActions('com_tswrent', 'category', $this->state->get('filter.category_id'));
        $user    = Factory::getApplication()->getIdentity();
        $toolbar = Toolbar::getInstance();

        ToolbarHelper::title(Text::_('COM_TSWRENT_MANAGER_PRODUCTS'), 'bookmark products');

        if ($canDo->get('core.create') || \count($user->getAuthorisedCategories('com_tswrent', 'core.create')) > 0) {
            $toolbar->addNew('product.add');
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
                    $childBar->publish('products.publish')->listCheck(true);

                    $childBar->unpublish('products.unpublish')->listCheck(true);
                }

                if ($this->state->get('filter.published') != -1) {
                    if ($this->state->get('filter.published') != 2) {
                        $childBar->archive('products.archive')->listCheck(true);
                    } elseif ($this->state->get('filter.published') == 2) {
                        $childBar->publish('publish')->task('products.publish')->listCheck(true);
                    }
                }

                $childBar->checkin('products.checkin');

                if ($this->state->get('filter.published') != -2) {
                    $childBar->trash('products.trash')->listCheck(true);
                }
            }

            if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
                $toolbar->delete('products.delete', 'JTOOLBAR_EMPTY_TRASH')
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

        if ($user->authorise('core.admin', 'com_tswrent') || $user->authorise('core.options', 'com_tswrent')) {
            $toolbar->preferences('com_tswrent');
        }

        $toolbar->help('Tswrent');
    }
}
