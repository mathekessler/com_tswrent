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

$app = Factory::getApplication();
$input = $app->input;

$assoc = Associations::isEnabled();

$this->ignore_fieldsets = ['item_associations'];
$this->useCoreUI = true;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate')
	->useScript('inlinehelp');

?>
<form action="<?php echo Route::_('index.php?option=com_tswrent&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="contact-form" class="form-validate">
	<div><? echo var_dump($data);echo 'test';?>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => '']); ?>

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'graduation', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_OPTIONS') : Text::_('COM_TSWRENT_FIELDSET_OPTIONS')); ?>
				<div class="row">
					<div class="col-md-12">
						<fieldset id="fieldset-detailsdata" class="options-form">
							<legend><?php echo Text::_('COM_TSWRENT_FIELDSET_GRADUATION'); ?></legend>
							<div>
								<a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_tswrent&view=graduations'); ?>">
									<span class="icon-plus icon-fw" aria-hidden="ture"></span>
									<?php echo Text::_('COM_TSWRENT_MANAGE_GRADUATION'); ?>	
								</a>	
							</div>
						</fieldset>
					</div>
				</div>	
			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'tswrent', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_TSWRENT') : Text::_('COM_TSWRENT_FIELDSET_TSWRENT')); ?>
			
			<?php echo HTMLHelper::_('uitab.endTab'); ?>	
			
			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'customer', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_CUSTOMER') : Text::_('COM_TSWRENT_FIELDSET_CUSTOMER')); ?>
			
			<?php echo HTMLHelper::_('uitab.endTab'); ?>
			
			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'supplier', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_SUPPLIER') : Text::_('COM_TSWRENT_FIELDSET_SUPPLIER')); ?>
				
			<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
