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
use Joomla\CMS\HTML\HTMLHelper;


$user       = Factory::getUser();
$userId     = $user->get('id');
$product_ids = $displayData['products'];
$listDirn= $displayData['listDirn'];
$listOrder= $displayData['listOrder']; 

?>


        <table class="table">
            <caption class="visually-hidden">
                <?php echo Text::_('COM_TSWRENT_PRODUCTS_TABLE_CAPTION'); ?>,
                <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
            </caption>
            <thead>
                <tr>
                    <th scope="col" class="d-none d-md-table-cell">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                    </th>
                    <th scope="col" class="d-none d-md-table-cell">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TSWRENT_HEADING_PRICE', 'a.price', $listDirn, $listOrder); ?>
                    </th>
                    <th scope="col" class="d-none d-md-table-cell">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TSWRENT_HEADING_STOCK', 'a.stock', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($product_ids as $i => $item) :
                    $canCreate  = $user->authorise('core.create');
                    $canEdit    = $user->authorise('core.edit');
                    $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || is_null($item->checked_out);
                    $canChange  = $user->authorise('core.edit.state') && $canCheckin;
                    ?>
                    <tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php echo $item->catid; ?>">
                        <th scope="row">
                            <div >
                                    <?php echo $this->escape($item->title); ?>
                            </div>   
                        </th>
                        <td>
                            <div >
                                <?php echo $this->escape($item->price); ?>
                            </div>
                        </td>
                        <td>
                            <div >
                                <?php echo $this->escape($item->stock); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


