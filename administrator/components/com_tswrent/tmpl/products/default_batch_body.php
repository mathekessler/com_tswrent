<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   (C) 2015 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;


use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

$published = (int) $this->state->get('filter.published');
?>

<div class="p-3">
    <div class="row">
        <div class="form-group col-md-6">
            <div class="controls">
                <?php echo HTMLHelper::_('tswrent.brand'); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <?php if ($published >= 0) : ?>
            <div class="form-group col-md-6">
                <div class="controls">
                    <?php echo LayoutHelper::render('joomla.html.batch.item', ['extension' => 'com_tswrent']); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

