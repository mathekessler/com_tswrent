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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

?>

<form action="<?php echo Route::_('index.php?option=com_tswrent'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php 
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table itemList" id="productList">
                        <caption class="visually-hidden">
                            <?php echo Text::_('COM_TSWRENT_PRODUCTS_TABLE_CAPTION'); ?>,
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" class="w-1 text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-10 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_TSWRENT_HEADING_BRAND', 'brand_title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-3 text-center d-none d-md-table-cell">
                                    <span title="<?php echo Text::_('COM_TSWREN_SUPPLIERS'); ?>"><?php echo Text::_('COM_TSWRENT_SUPPLIERS'); ?></span>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) :
                                $canCreate  = $user->authorise('core.create');
                                $canEdit    = $user->authorise('core.edit');
                                $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || is_null($item->checked_out);
                                $canChange  = $user->authorise('core.edit.state') && $canCheckin;
                                ?>
                                <tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php echo $item->catid; ?>">
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->title); ?>
                                    </td>
                                     <td class="text-center">
                                        <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'products.', $canChange); ?>
                                    </td>
                                    <th scope="row">
                                        <div class="break-word">
                                            <?php if ($canEdit) : ?>
                                                <a href="<?php echo Route::_('index.php?option=com_tswrent&task=product.edit&id=' . (int) $item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->title); ?>">
                                                    <?php echo $this->escape($item->title); ?></a>
                                            <?php else : ?>
                                                <?php echo $this->escape($item->title); ?>
                                            <?php endif; ?>
                                            <div class="small break-word">
                                                <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                            </div>
                                            <div class="small">
                                                <?php echo Text::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="small d-none d-md-table-cell">
                                        <?php echo $item->brand_title; ?>
                                    </td>
                                    <td class="text-center btns d-none d-md-table-cell itemnumber">
                                        <a class="btn <?php echo (!empty($item->count_suppliers) && $item->count_suppliers > 0) ? 'btn-success' : 'btn-secondary'; ?>" href="<?php echo Route::_('index.php?option=com_tswrent&view=suppliers&filter[brand_id]=' . (int) $item->brand_id ); ?>"
                                        aria-describedby="tip-suppliers<?php echo $i; ?>">
                                            <?php echo (int) ($item->count_suppliers ?? 0); ?>
                                        </a>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?php echo $item->id; ?>
                                    </td>
                                    
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php // Load the pagination. ?>
                    <?php echo $this->pagination->getListFooter(); ?>

                    <?php // Load the batch processing form. ?>
                    <?php
                    if (
                        $user->authorise('core.create', 'com_tswrent')
                        && $user->authorise('core.edit', 'com_tswrent')
                        && $user->authorise('core.edit.state', 'com_tswrent')
                    ) : ?>
                        <?php echo HTMLHelper::_(
                            'bootstrap.renderModal',
                            'collapseModal',
                            [
                                'title' => Text::_('COM_TSWRENT_BATCH_OPTIONS'),
                                'footer' => $this->loadTemplate('batch_footer')
                            ],
                            $this->loadTemplate('batch_body')
                        ); ?>
                    <?php endif; ?>
                <?php endif; ?>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
