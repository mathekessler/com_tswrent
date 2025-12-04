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

<form action="<?php echo Route::_('index.php?option=com_tswrent&view=brands'); ?>" method="post" name="adminForm" id="adminForm">
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
                    <table class="table" id="brandList">
                        <caption class="visually-hidden">
                            <?php echo Text::_('COM_TSWRENT_BRANDS_TABLE_CAPTION'); ?>,
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" class="w-1 text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell">
                                    <span ><?php echo Text::_('COM_TSWRENT_HEADING_WEBPAGE'); ?></span>
                                </th>
                                <th scope="col" class="w-3 text-center d-none d-md-table-cell">
                                    <span  title="<?php echo Text::_('COM_TSWRENT_PRODUCTS'); ?>"><?php echo Text::_('COM_TSWRENT_PRODUCTS'); ?></span>
                                </th>
                                <th scope="col" class="w-3 text-center d-none d-md-table-cell"> 
                                    <span  title="<?php echo Text::_('COM_TSWRENT_SUPPLIERS'); ?>"><?php echo Text::_('COM_TSWRENT_SUPPLIERS'); ?></span>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) :
                                $canCreate  = $user->authorise('core.create','com_tswrent');
                                $canEdit    = $user->authorise('core.edit', 'com_tswrent');
                                $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || is_null($item->checked_out);
                                $canChange  = $user->authorise('core.edit.state', 'com_tswrent') && $canCheckin;
                                ?>
                                 <tr class="row<?php echo $i % 2; ?>">
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->title); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'brands.', $canChange);?>
                                    </td>
                                    <th scope="row" class="has-context">
                                        <div>
                                            <?php if ($item->checked_out) : ?>
                                                <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'brands.', $canCheckin); ?>
                                            <?php endif; ?>
                                            <?php if ($canEdit) : ?>
                                                <a href="<?php echo Route::_('index.php?option=com_tswrent&view=brand&id=' . (int) $item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->title); ?>">
                                                    <?php echo $this->escape($item->title); ?></a>
                                            <?php else : ?>
                                                <?php echo $this->escape($item->title); ?>
                                            <?php endif; ?>
                                        </div>
                                    </th>
                                    <td class="small d-none d-md-table-cell">
                                    <a href="<?php echo $item->webpage; ?>"><?php echo $item->webpage; ?></a>
                                    </td>
                                    </td>
                                    <td class="text-center btns d-none d-md-table-cell itemnumber">
                                        <a class="btn <?php echo ($item->count_products > 0) ? 'btn-success' : 'btn-secondary'; ?>" href="<?php echo Route::_('index.php?option=com_tswrent&view=products&filter[brand_id]=' . (int) $item->id  ); ?>"
                                        aria-describedby="tip-publish<?php echo $i; ?>">
                                            <?php echo $item->count_products; ?>
                                        </a>
                                    </td>
                                    <td class="text-center btns d-none d-md-table-cell itemnumber">
                                        <a class="btn <?php echo ($item->count_suppliers > 0) ? 'btn-success' : 'btn-secondary'; ?>" href="<?php echo Route::_('index.php?option=com_tswrent&view=suppliers&filter[brand_id]=' . (int) $item->id ); ?>"
                                        aria-describedby="tip-publish<?php echo $i;?>">
                                            <?php echo $item->count_suppliers;?>
                                        </a>
                                    </td>

                                    <td class="d-none d-md-table-cell">
                                        <?php echo $item->id; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
					<?php echo $this->pagination->getListFooter(); ?>
				
					<?php echo HTMLHelper::_(
						'bootstrap.renderModal',
						'collapseModal',
						[
							'title'  => Text::_('COM_TSWRENT_BATCH_OPTIONS'),
							'footer' => $this->loadTemplate('batch_footer'),
						],
						$this->loadTemplate('batch_body')
					); ?>

				<?php endif; ?>
				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
