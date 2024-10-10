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
$wa->useScript('core')
    ->useScript('modal-content-select');

	$function  = $app->getInput()->getCmd('function', 'jSelectMenuItem');
	$listOrder = $this->escape($this->state->get('list.ordering'));
	$listDirn  = $this->escape($this->state->get('list.direction'));
	$link      = 'index.php?option=com_tswrent&view=products&layout=modal&tmpl=component&' . Session::getFormToken() . '=1&function=' . $function;

?>
<div class="container-popup">

	<form action="<?php echo Route::_('index.php?option=com_tswrent&view=products&layout=modal&tmpl=component&' . Session::getFormToken() . '=1'); ?>" method="post" name="adminForm" id="adminForm">

		<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-warning">
				<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table" id="productList">
				<caption class="visually-hidden">
					<?php echo Text::_('COM_TSWRENT_TABLE_CAPTION'); ?>
					<span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
					<span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
				</caption>
				<thead>
					<tr>
						<th scope="col"  class="w-10 d-none d-md-table-cell"></th>
						<th scope="col"class="w-10 d-none d-md-table-cell"></th>
					</tr>
				</thead>
				<tbody>
					<?php
					// Iteration durch die Elemente
					foreach ($this->items as $i => $item) :
						// Erstelle den Link zum Produkt
						$link = 'index.php?Productid=' . $item->id;
						$itemHtml = '<a href="' . $link . '">' . $this->escape($item->title) . '</a>';
						$attribs = 'data-content-select data-content-type="com_tswrent.product"'
						. ' data-id="' . $item->id . '"'
						. ' data-title="' . $this->escape($item->title) . '"'
						. ' data-cat-id="' . $this->escape($item->catid) . '"'
						. ' data-uri="' . $this->escape($link) . '"'
						. ' data-html="' . $this->escape($itemHtml) . '"';
					?>
						<tr class="row<?php echo $i % 2; ?>">
							<th scope="row">
							<a href="javascript:void(0)" <?php echo $attribs; ?>>
                               <?php echo $this->escape($item->title); ?></a>
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
