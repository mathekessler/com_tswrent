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

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate')

?>
<form action="<?php echo Route::_('index.php?option=com_tswrent&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="supplier-form" aria-label="<?php echo Text::_('COM_TSWRENT_BRAND_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>" class="form-validate">	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details']); ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_DETAIL') : Text::_('COM_TSWRENT_FIELDSET_DETAIL')); ?>	
			<div class="row">
				<div class="col-md-12">
					<div id="j-main-container" class="j-main-container">
						<div class="col-12 col-md-6">
							<div id="j-main-container" class="j-main-container">	
								<?php /** load brand Address Layout **/
									$data_address = [
										'address' => $this->item,
										'view' => [
											'view' => 'supplier',
											'id' => $this->item->id
										],
										'logo' => $this->item->brand_logo							
									];
									echo LayoutHelper::render('address', $data_address, 'com_tswrent.layouts');  // Load the layout 
								?>
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
