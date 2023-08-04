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

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns');

$canChange = true;
$assoc = Associations::isEnabled();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

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
					<table class="table" id="supplieremployeeList">
						<caption class="visually-hidden">
							<?php echo Text::_('COM_TSWRENT_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
						</caption>
						<thead>
							<tr>
								<td style="width:1%" class="text-center">
									<?php echo HTMLHelper::_('grid.checkall'); ?>
								</td>
								<th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_TSWRENT_TABLE_TABLEHEAD_NAME', 'a.title', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" style="width:1%; min-width:85px" class="text-center">
									<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
								</th>
								<th scope="col">
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
								<td class="supplieremployee text-center d-none d-md-table-cell">
									<?php
									$iconClass = '';
									if (!$canChange) {
										$iconClass = ' inactive';
									} else if (!$saveOrder) {
										$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::_('tooltipText', 'JORDERINGDISABLED');
									}
									?>
									<span class="sortable-handler<?php echo $iconClass; ?>">
										<span class="icon-menu" aria-hidden="true"></span>
									</span>
									<?php if ($canChange && $saveOrder) : ?>
										<input type="text" style="display:none" name="order[]" size="5"
											value="<?php echo $item->ordering; ?>" class="width-20 text-area-order">
									<?php endif; ?>
								</td>
								<td class="text-center">
									<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
								</td>
								<th scope="row" class="has-context">
									<?php if ($item->checked_out) : ?>
										<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'supplieremployees.', true); ?>
									<?php endif; ?>
									<div>
										<?php echo $this->escape($item->title); ?>
									</div>
									<?php $editIcon = '<span class="fa fa-pencil-square mr-2" aria-hidden="true"></span>'; ?>
									<a class="hasTooltip" href="<?php echo Route::_('index.php?option=com_supplieremployees&task=supplieremployee.edit&id=' . (int) $item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape(addslashes($item->name)); ?>">
										<?php echo $editIcon; ?><?php echo $this->escape($item->title); ?></a>

									<div class="small">
										<?php echo Text::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
									</div>
								</th>
								<td class="text-center">
									<?php
									echo HTMLHelper::_('jgrid.published', $item->published, $i, 'supplieremployees.', $canChange, 'cb', $item->publish_up, $item->publish_down);
									?>

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
