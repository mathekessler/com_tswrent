<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;

$app = Factory::getApplication();

$wa = $this->document->getWebAssetManager();
$wa ->useScript('com_tswrent.admin-modal')
	->useScript('core')
    ->useScript('modal-content-select');

// @todo: Use of Function and Editor is deprecated and should be removed in 6.0. It stays only for backward compatibility.
$function  = $app->getInput()->get('function', 'jSelectCustomer', 'cmd');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$link      = 'index.php?option=com_tswrent&view=customers&layout=modal&tmpl=component&' . Session::getFormToken() . '=1&function=' . $function;

?>
<div class="container-popup">

	<form action="<?php echo Route::_($link); ?>" method="post" name="adminForm" id="adminForm">

		<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-warning">
				<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-sm">
                <caption class="visually-hidden">
                    <?php echo Text::_('COM_MENUS_ITEMS_TABLE_CAPTION'); ?>,
                        <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                        <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                </caption>
				<thead>
					<tr>
                        <th scope="col" class="title">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" class="w-1 d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                        </th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
                    
                    <?php
                        $link     = 'index.php?Customerid=' . $item->id;
                        $itemHtml = '<a href="' . $link . '">' . $item->title . '</a>';
                        $attribs  = 'data-content-select data-content-type="com_menus.item"'
                            . 'data-function="' . $this->escape($function) . '"'
                            . ' data-id="' . $item->id . '"'
                            . ' data-title="' . $this->escape($item->title) . '"'
                            . ' data-uri="' . $this->escape($link) . '"'
                            . ' data-language="' . $this->escape($language) . '"'
                            . ' data-html="' . $this->escape($itemHtml) . '"';
                       ?>

					<tr class="row<?php echo $i % 2; ?>">
						<th scope="row">
							<a class="select-link" href="javascript:void(0)" <?php echo $attribs; ?> >
								<?php echo $this->escape($item->title); ?>
							</a>
						</th>
						<td class="d-none d-md-table-cell">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

		<?php endif; ?>
       
        <?php // load the pagination. ?>
        <?php echo $this->pagination->getListFooter(); ?>
		
        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
		<?php echo HTMLHelper::_('form.token'); ?>
     
	</form>
</div>