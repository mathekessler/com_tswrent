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
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_TSWRENT_SUPPLIERS',
	'formURL'    => 'index.php?option=com_tswrent&view=suppliers',
	'icon'       => 'icon-rss newsfeeds',
];

if (Factory::getApplication()->getIdentity()->authorise('core.create', 'com_tswrent')) {
	$displayData['createURL'] = 'index.php?option=com_tswrent&task=supplier.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
