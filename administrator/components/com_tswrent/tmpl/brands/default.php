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


$user       = Factory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';

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
                    <table class="table">
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
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_TSWRENT_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell">
                                    <span ><?php echo Text::_('COM_TSWRENT_HEADING_WEBSITE'); ?></span>
                                </th>
                                <th scope="col" class="w-3 text-center d-none d-md-table-cell">
                                    <span class="icon-check" aria-hidden="true" title="<?php echo Text::_('COM_TSWREN_COUNT_PUBLISHED_ITEMS'); ?>"></span>
                                    <span class="visually-hidden"><?php echo Text::_('COM_TSWRENTt_COUNT_PUBLISHED_ITEMS'); ?></span>
                                </th>
                                <th scope="col" class="w-3 text-center d-none d-md-table-cell">
                                    <span class="icon-times" aria-hidden="true" title="<?php echo Text::_('COM_TSWREN_COUNT_UNPUBLISHED_ITEMS'); ?>"></span>
                                    <span class="visually-hidden"><?php echo Text::_('COM_TSWREN_COUNT_UNPUBLISHED_ITEMS'); ?></span>
                                </th>
                                <th scope="col" class="w-3 text-center d-none d-md-table-cell">
                                    <span class="icon-folder icon-fw" aria-hidden="true" title="<?php echo Text::_('COM_TSWREN_COUNT_ARCHIVED_ITEMS'); ?>"></span>
                                    <span class="visually-hidden"><?php echo Text::_('COM_TSWREN_COUNT_ARCHIVED_ITEMS'); ?></span>
                                </th>
                                <th scope="col" class="w-3 text-center d-none d-md-table-cell">
                                    <span class="icon-trash" aria-hidden="true" title="<?php echo Text::_('COM_TSWREN_COUNT_TRASHED_ITEMS'); ?>"></span>
                                    <span class="visually-hidden"><?php echo Text::_('COM_TSWREN_COUNT_TRASHED_ITEMS'); ?></span>
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
                                        <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'brands.', $canChange);?>
                                    </td>
                                    <th scope="row" class="has-context">
                                        <div>
                                            <?php if ($item->checked_out) : ?>
                                                <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'brands.', $canCheckin); ?>
                                            <?php endif; ?>
                                            <?php if ($canEdit) : ?>
                                                <a href="<?php echo Route::_('index.php?option=com_tswrent&task=brand.edit&id=' . (int) $item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->title); ?>">
                                                    <?php echo $this->escape($item->title); ?></a>
                                            <?php else : ?>
                                                <?php echo $this->escape($item->title); ?>
                                            <?php endif; ?>
                                        </div>
                                    </th>
                                    <td class="small d-none d-md-table-cell">
                                    <a href="<?php echo $item->website; ?>"><?php echo $item->website; ?></a>
                                    </td>
                                    </td>
                                    <td class="text-center btns d-none d-md-table-cell itemnumber">
                                        <a class="btn <?php echo ($item->count_published > 0) ? 'btn-success' : 'btn-secondary'; ?>" href="<?php echo Route::_('index.php?option=com_tswrent&view=products&filter[brand_id]=' . (int) $item->id . '&filter[published]=1'); ?>"
                                        aria-describedby="tip-publish<?php echo $i; ?>">
                                            <?php echo $item->count_published; ?>
                                        </a>
                                        <div role="tooltip" id="tip-publish<?php echo $i; ?>">
                                            <?php echo Text::_('COM_TSWRENT_COUNT_PUBLISHED_ITEMS'); ?>
                                        </div>
                                    </td>
                                    <td class="text-center btns d-none d-md-table-cell itemnumber">
                                        <a class="btn <?php echo ($item->count_unpublished > 0) ? 'btn-danger' : 'btn-secondary'; ?>" href="<?php echo Route::_('index.php?option=com_tswrent&view=products&filter[brand_id]=' . (int) $item->id . '&filter[published]=0'); ?>"
                                        aria-describedby="tip-unpublish<?php echo $i; ?>">
                                            <?php echo $item->count_unpublished; ?>
                                        </a>
                                        <div role="tooltip" id="tip-unpublish<?php echo $i; ?>">
                                            <?php echo Text::_('COM_TSWRENT_COUNT_UNPUBLISHED_ITEMS'); ?>
                                        </div>
                                    </td>
                                    <td class="text-center btns d-none d-md-table-cell itemnumber">
                                        <a class="btn <?php echo ($item->count_archived > 0) ? 'btn-info' : 'btn-secondary'; ?>" href="<?php echo Route::_('index.php?option=com_tswrent&view=products&filter[brand_id]=' . (int) $item->id . '&filter[published]=2'); ?>"
                                        aria-describedby="tip-archived<?php echo $i; ?>">
                                            <?php echo $item->count_archived; ?>
                                        </a>
                                        <div role="tooltip" id="tip-archived<?php echo $i; ?>">
                                            <?php echo Text::_('COM_TSWRENT_COUNT_ARCHIVED_ITEMS'); ?>
                                        </div>
                                    </td>
                                    <td class="text-center btns d-none d-md-table-cell itemnumber">
                                        <a class="btn <?php echo ($item->count_trashed > 0) ? 'btn-dark' : 'btn-secondary'; ?>" href="<?php echo Route::_('index.php?option=com_tswrent&view=products&filter[brand_id]=' . (int) $item->id . '&filter[published]=-2'); ?>"
                                        aria-describedby="tip-trashed<?php echo $i; ?>">
                                            <?php echo $item->count_trashed; ?>
                                        </a>
                                        <div role="tooltip" id="tip-trashed<?php echo $i; ?>">
                                            <?php echo Text::_('COM_TSWRENT_COUNT_TRASHED_ITEMS'); ?>
                                        </div>
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
