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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate')
	->useScript('inlinehelp');

?>
<form action="<?php echo Route::_('index.php?option=com_tswrent&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="supplier-form" class="form-validate">	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'address']); ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'address', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_DETAIL') : Text::_('COM_TSWRENT_FIELDSET_DETAIL')); ?>	
			<div class="row">
				<div class="col-md-12">
					<div id="j-main-container" class="j-main-container">
						<?php /** load Suppliers Layout **/
							$data_address= ['address' => $this->item];  // Prepare data to pass
							echo \Joomla\CMS\Layout\LayoutHelper::render('address', $data_address, 'com_tswrent.layouts');  // Load the layout 
						?>
						<?php echo Text::_('COM_TSWRENT_DOWNLOAD_INFORMATION_AS'); ?>
						<a href="<?php echo Route::_('index.php?option=com_tswrent&view=contact&&id=' . $this->item->id . '&format=vcf'); ?>">
						<?php echo Text::_('COM_TSWRENT_VCARD'); ?></a>
					</div>
				</div>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'suppliers', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_SUPPLIERS') : Text::_('COM_TSWRENT_FIELDSET_SUPPLIERS')); ?>				
			<div class="row">
				<div class="col-md-12">
					<div id="j-main-container" class="j-main-container">
						<?php /** load Suppliers Layout **/
							$data_supplier= ['supplier' => $this->item->supplier_ids];  // Prepare data to pass
							echo \Joomla\CMS\Layout\LayoutHelper::render('suppliers', $data_supplier, 'com_tswrent.layouts');  // Load the layout 
						?>
					</div>
				</div>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'customer', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_CUSTOMERS') : Text::_('COM_TSWRENT_FIELDSET_CUSTOMERS')); ?>				
			<div class="row">
				<div class="col-md-12">
					<div id="j-main-container" class="j-main-container">
						<?php /** load Suppliers Layout **/
							$data_customer = ['customer' => $this->item->customer_ids];  // Prepare data to pass
							echo \Joomla\CMS\Layout\LayoutHelper::render('customers', $data_customer, 'com_tswrent.layouts');  // Load the layout 
						?>
					</div>
				</div>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
