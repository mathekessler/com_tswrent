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
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<div class="col-12 col-md-6">
					<div class="control-group">
						<div class="controls has-success">
							<?php echo $this->item->title ?>
						</div>
						<!-- Address Block -->
						<div class="com-contact__info">
                			<?php echo $this->loadTemplate('address'); ?>
                    		<?php echo Text::_('COM_TSWRENT_DOWNLOAD_INFORMATION_AS'); ?>
                   			 <a href="<?php echo Route::_('index.php?option=com_tswrent&view=supplier&&id=' . $this->item->id . '&format=vcf'); ?>">
                    		<?php echo Text::_('COM_TSWRENT_VCARD'); ?></a>
			            </div>
					</div>
				</div>
				<div class="col-6 ">
					<div class="row">
						<!-- Brand Block -->
						<?php echo $this->loadTemplate('brand'); ?>
					</div>
				</div>
				<div class="col-6 ">
					<div class="row">
						<!-- Contact Block -->
						<?php echo $this->loadTemplate('contact'); ?>
					</div>
				</div>
				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
