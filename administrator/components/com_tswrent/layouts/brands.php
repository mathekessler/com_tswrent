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
use Joomla\CMS\Language\Text;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\Router\Route;

$user       = Factory::getUser();
$userId     = $user->get('id');
$brand = $displayData['brand'];
?>

<div class="col-6 ">
    <div class="row">
        <table class="table"> 
            <caption class="visually-hidden">
                <?php echo Text::_('COM_TSWRENT_BRANDS_TABLE_CAPTION'); ?>,
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
                <?php foreach ($brand as $brand) :
                    $canCreate  = $user->authorise('core.create','com_tswrent');
                    $canEdit    = $user->authorise('core.edit', 'com_tswrent');
                    $canCheckin = $user->authorise('core.manage', 'com_checkin') || $this->item->checked_out == $userId || is_null($this->item->checked_out);
                    $canChange  = $user->authorise('core.edit.state', 'com_tswrent') && $canCheckin;
                    ?>
                    <tr class="row<?php echo $i % 2; ?>">

                        <th scope="row" >
                        <a href="<?php echo Route::_('index.php?option=com_tswrent&view=brand&id=' . (int) $brand->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($brand->title); ?>">
                                    <?php echo $this->escape($brand->title); ?></a>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo $brand->webpage; ?>" target="_blank" rel="noopener noreferrer" itemprop="url">
                            <?php echo PunycodeHelper::urlToUTF8($brand->webpage); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>