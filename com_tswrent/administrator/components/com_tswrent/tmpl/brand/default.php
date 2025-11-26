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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\String\PunycodeHelper;

$listOrder = $this->state->get('list.ordering', 'a.title');
$listDirn = $this->state->get('list.direction', 'asc');

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate')

?>
<form action="<?php echo Route::_('index.php?option=com_tswrent&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="supplier-form" aria-label="<?php echo Text::_('COM_TSWRENT_BRAND_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>" class="form-validate">	
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
						<div>
							<?php /** load Suppliers Layout **/
								$data = ['supplier' => $this->item->supplier_ids];  // Prepare data to pass
								echo LayoutHelper::render('suppliers', $data, 'com_tswrent.layouts');  // Load the layout 
							?>
						</div>
					</div>
				</div>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'products', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_PRODUCTS') : Text::_('COM_TSWRENT_FIELDSET_PRODUCTS')); ?>	
			<div class="com-contact__info">
				<?php /** load Suppliers Layout **/
					$data = [
						'products' => $this->item->product_ids,
						'showLinks' => true,
    					'showActions' => true,
						'listOrder' => $this->state->get('list.ordering', 'a.title'),
    					'listDirn' => $this->state->get('list.direction', 'asc')
					];  // Prepare data to pass
					echo LayoutHelper::render('products', $data, 'com_tswrent.layouts');  // Load the layout 
				?>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>">
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
