<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Form\Form;

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns');
$wa->useScript('com_tswrent.admin-orders');

$canChange = true;
$assoc = Associations::isEnabled();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

// Prepare the orderstate field for each item
// Get the form field template once
$form = Form::getInstance(
	'order',
	JPATH_COMPONENT_ADMINISTRATOR . '/forms/order.xml');

?>

<form action="<?php echo Route::_('index.php?option=com_tswrent'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
				<?php if (empty($this->items)) : ?>
					<div class="alert alert-warning">
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<table class="table" id="orderList">
						<caption class="visually-hidden">
							<?php echo Text::_('COM_TSWRENT_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
						</caption>
						<thead>
							<tr>
								<td class="w-1 text-center">
									<?php echo HTMLHelper::_('grid.checkall'); ?>
								</td>
								<th scope="col" class="w-10 d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class=" w-1 text-center">
									<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.orderstate', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="w-10 text-center">
									<?php echo Text::_('COM_TSWRENT_HEADING_ACTIONS'); ?>
								</th>
								<th scope="col" class="w-1">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$n = count($this->items);
						foreach ($this->items as $i => $item) :
							?>
							<tr class="row<?php echo $i % 2; ?>">
								<td class="text-center">
									<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
								</td>
								<th scope="row" >
									<?php $editIcon = '<span class="icon-edit" aria-hidden="true"></span>'; ?>
									<a class="hasTooltip" href="<?php echo Route::_('index.php?option=com_tswrent&task=order.edit&id=' . (int) $item->id); ?>" title="<?php echo Text::_('JACTION_EDIT') . ' ' . $this->escape($item->title); ?>">
										<?php echo $this->escape($item->title); ?><?php echo $editIcon; ?></a>
								</th>
								<td class="text-center">
									<?php echo $item->orderstate_input ?? ''; ?>
								</td>
								<td class="text-center">
									<?php
									// Der Link zum PDF-Download. Die Logik, welches PDF erzeugt wird,
									// liegt im Controller und hängt vom aktuellen orderstate ab.
									?>
									<a class="btn btn-small btn-secondary" href="<?php echo Route::_('index.php?option=com_tswrent&task=order.generatePdf&id=' . (int) $item->id); ?>" target="_blank" rel="noopener noreferrer"><span class="icon-file-pdf" aria-hidden="true"></span> <?php echo Text::_('COM_TSWRENT_GENERATE_PDF'); ?></a>
								</td>
								<td class="d-none d-md-table-cell">
									<?php echo $item->id; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>

					<?php echo $this->pagination->getListFooter(); ?>
				
					<?php echo HTMLHelper::_(
						'bootstrap.renderModal',
						'collapseModal',
						[
							'title'  => Text::_('COM_TSWRENT_BATCH_OPTIONS'),
							'footer' => $this->loadTemplate('batch_footer'),
						],
						$this->loadTemplate('batch_body')
					); ?>

				<?php endif; ?>
				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
