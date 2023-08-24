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

?>

<div class="row">
    <div class="col-md-12">
        <div id="j-main-container" class="j-main-container">
                <table class="table" id="productList">
                    <caption class="visually-hidden">
                        <?php echo Text::_('COM_TSWRENT_PRODUCTS_TABLE_CAPTION'); ?>,
                        <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                        <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                    </caption>
                    <thead>
                        <tr>
                            <th scope="col">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TSWRENT_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TSWRENT_HEADING_PRICE', 'a.price', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TSWRENT_HEADING_PRICE', 'a.price', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->item->product_ids as $i => $item) :
                            $canCreate  = $user->authorise('core.create');
                            $canEdit    = $user->authorise('core.edit');
                            $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || is_null($item->checked_out);
                            $canChange  = $user->authorise('core.edit.state') && $canCheckin;
                            ?>
                            <tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php echo $item->catid; ?>">
                                <th scope="row">
                                    <div class="break-word">
                                            <?php echo $this->escape($item->title); ?>
                                    </div>   
                                </th>
                                <td>
                                    <div class="break-word">
                                        <?php echo $this->escape($item->price); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="break-word">
                                        <?php echo $this->escape($item->unit); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        </div>
        </div>
    </div>
                    </div>