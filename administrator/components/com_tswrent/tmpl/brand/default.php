<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\String\PunycodeHelper;



$user       = Factory::getUser();
$userId     = $user->get('id');

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate')

?>
<form action="<?php echo Route::_('index.php?option=com_tswrent&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="supplier-form" class="form-validate">	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details']); ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_DETAIL') : Text::_('COM_TSWRENT_FIELDSET_DETAIL')); ?>	
			<div class="row">
				<div class="col-md-12">
					<div id="j-main-container" class="j-main-container">
						<div class="col-12 col-md-6">
							<div class="control-group">
								<dl class="com-tswrent__address supplier-address dl-horizontal" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
									</dt>
										<dt>
											<span aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_WEBPAGE'); ?></span>
										</dt>
										<dd>
											<span class="contact-webpage">
												<?php echo $this->item->title; ?></a>
											</span>
										</dd>
										</dt>
									<dt>
										<span class="icon-home" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_WEBPAGE'); ?></span>
									</dt>
									<dd>
										<span class="contact-webpage">
											<a href="<?php echo $this->item->webpage; ?>" target="_blank" rel="noopener noreferrer" itemprop="url">
											<?php echo PunycodeHelper::urlToUTF8($this->item->webpage); ?></a>
										</span>
									</dd>
								</dl>
							</div>
						</div>
						<div class="col-6 ">
							<div class="row">
								<!-- Address Block -->
								<table class="table">
									<caption class="visually-hidden">
										<?php echo Text::_('COM_TSWRENT_SUPPLIERS_TABLE_CAPTION'); ?>,
										<span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
										<span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
									</caption>
									<thead>
										<tr>
											<th scope="col" class="w-10  d-none d-md-table-cell">
												<span ><?php echo Text::_('COM_TSWRENT_HEADING_TITLE'); ?></span>
											</th>
											<th scope="col" class="w-5 text-center d-none d-md-table-cell">
												<span ><?php echo Text::_('COM_TSWRENT_HEADING_WEBPAGE'); ?></span>
											</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($this->item->supplier_ids as $supplier) :
											$canCreate  = $user->authorise('core.create','com_tswrent');
											$canEdit    = $user->authorise('core.edit', 'com_tswrent');
											$canCheckin = $user->authorise('core.manage', 'com_checkin') || $this->item->checked_out == $userId || is_null($this->item->checked_out);
											$canChange  = $user->authorise('core.edit.state', 'com_tswrent') && $canCheckin;
											?>
											<tr class="row<?php echo $i % 2; ?>">

												<th scope="row" >
												<a href="<?php echo Route::_('index.php?option=com_tswrent&view=supplier&id=' . (int) $supplier->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($supplier->title); ?>">
															<?php echo $this->escape($supplier->title); ?></a>
													<?php echo $brand->title ; ?>
												</td>
												<td class="text-center">
													<a href="<?php echo $supplier->webpage; ?>" target="_blank" rel="noopener noreferrer" itemprop="url">
													<?php echo PunycodeHelper::urlToUTF8($supplier->webpage); ?></a>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'products', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_PRODUCTS') : Text::_('COM_TSWRENT_FIELDSET_PRODUCTS')); ?>	
			<div class="com-contact__info">
            	<?php echo $this->loadTemplate('products'); ?>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
