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
use \Joomla\CMS\Layout\LayoutHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

?>
<form action="<?php echo Route::_('index.php?option=com_tswrent&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="customer-form" aria-label="<?php echo Text::_('COM_TSWRENT_CUSTOMER_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>"class="form-validate">	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details']); ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_DETAIL') : Text::_('COM_TSWRENT_FIELDSET_DETAIL')); ?>	
			<div class="row">
				<div class="col-md-12">
					<div id="j-main-container" class="j-main-container">	
						<?php /** load customer Address Layout **/
							$data_address = [
										'address' => $this->item,
										'view' => [
											'view' => 'customer',
											'id' => $this->item->id
										],
										'logo' => $this->item->customer_logo							
									];// Prepare data to pass
							echo LayoutHelper::render('address', $data_address, 'com_tswrent.layouts');  // Load the layout 
							 
						?>
					</div>
				</div>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'contacts', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_CONTACTS') : Text::_('COM_TSWRENT_FIELDSET_CONTACTS')); ?>	
			<div class="row">
				<div class="col-md-12">
					<div id="j-main-container" class="j-main-container">	
						<?php /** load Customer Contact Layout **/
							$data_contact= ['contact' => $this->item->contact_ids];  // Prepare data to pass
							echo LayoutHelper::render('contacts', $data_contact, 'com_tswrent.layouts');  // Load the layout 
						?>
					</div>
				</div>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
