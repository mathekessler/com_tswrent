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
<form action="<?php echo Route::_('index.php?option=com_tswrent&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="customer-form" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details']); ?>

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_DETAIL') : Text::_('COM_TSWRENT_FIELDSET_DETAIL')); ?>
				<div class="row">
					<div class="col-md-12">
						<fieldset id="fieldset-detailsdata" class="options-form">
							<legend><?php echo Text::_('COM_TSWRENT_FIELDSET_DETAIL'); ?></legend>
							<div>
								<?php echo $this->form->renderFieldset('details'); ?>								
							</div>
						</fieldset>
					</div>
				</div>	
			<?php echo HTMLHelper::_('uitab.endTab'); ?>
					
			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'contact', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_CONTACT') : Text::_('COM_TSWRENT_FIELDSET_CONTACT')); ?>
			<div class="row">
					<div class="col-md-12">
						<fieldset id="fieldset-contactdata" class="options-form">
							<legend><?php echo Text::_('COM_TSWRENT_FIELDSET_CONTACT'); ?></legend>
							<div>
								<?php echo $this->form->renderFieldset('contact'); ?>								
							</div>
						</fieldset>
					</div>
				</div>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
				<div class="row">
					<div class="col-md-12">
						<fieldset id="fieldset-publishingdata" class="options-form">
							<legend><?php echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></legend>
							<div>
							<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
							</div>
						</fieldset>
					</div>
				</div>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
