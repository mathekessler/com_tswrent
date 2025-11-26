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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$factory_name= $this->state->params->get('title');

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate')
	->useScript('inlinehelp');

?>
<form action="<?php echo Route::_('index.php?option=com_tswrent&layout=edit'); ?>" method="post" name="adminForm" id="config-form" class="form-validate">
	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => '']); ?>

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'graduation', empty($this->item->id) ? Text::_('COM_TSWRENT_FIELDSET_GRADUATION') : Text::_('COM_TSWRENT_FIELDSET_GRADUATION')); ?>
				<div class="row">
					<div class="col-md-12">
						<fieldset id="fieldset-detailsdata" class="options-form">
							<div class="com_tswrent__graduation">
								<?php echo $this->loadTemplate('graduation'); ?>
							</div>	
							
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

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'tswrent', empty($this->item->id) ? Text::sprintf('COM_TSWRENT_FIELDSET_TSWRENT',$factory_name) : Text::sprintf('COM_TSWRENT_FIELDSET_TSWRENT',$factory_name)); ?>
				<div class="row"> 
					<div class="col-md-12">

					</div>
				</div> 
				
				<?php 
				/** load Contacts Layout **/
					
					$data_contact = ['contact' => $this->item->tswrentemployee];  // Prepare data to pass
					echo LayoutHelper::render('contacts', $data_contact, 'com_tswrent.layouts');  // Load the layout 
					?>
					<div>
					<a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_tswrent&view=contacts'); ?>">
						<span class="icon-plus icon-fw" aria-hidden="ture"></span>
						<?php echo Text::_('COM_TSWRENT_MANAGE_CONTACTS'); ?>	
					</a>	
				</div>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>	

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
