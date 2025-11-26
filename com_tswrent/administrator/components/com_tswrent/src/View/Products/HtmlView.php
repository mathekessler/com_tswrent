<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\View\Products;


use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use TSWEB\Component\Tswrent\Administrator\Model\ProductsModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View class for a list of products.
 *
 * @since  __BUMP_VERSION__
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
    public $activeFilters = [];

        /**
     * All transition, which can be executed of one if the items
     *
     * @var  array
     */
    protected $transitions = [];

    /**
     * An array of items
     *
     * @var    array
     * @since  __BUMP_VERSION__
     */
    protected $items = [];

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
     * Are hits being recorded on the site?
     *
     * @var   boolean
     * @since __BUMP_VERSION__
     */
    protected $hits = false;

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
    public function display($tpl = null): void
    {
        /** @var ProductsModel $model */
        $model               = $this->getModel();

        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();
        $this->state         = $model->getState();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();

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
        }

        // Add form control fields
        $this->filterForm
            ->addControlField('task', '')
            ->addControlField('boxchecked', '0');

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
    protected function addToolbar(): void
    {
        $canDo   = ContentHelper::getActions('com_tswrent', 'category', $this->state->get('filter.category_id'));
        $user    = $this->getCurrentUser();
        $toolbar = $this->getDocument()->getToolbar();

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
