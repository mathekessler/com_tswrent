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
	'textPrefix' => 'COM_TSWRENT_customerS',
	'formURL' => 'index.php?option=com_tswrent',
	'helpURL' => 'https://github.com/astridx/boilerplate#readme',
	'icon' => 'icon-folde customers',
];

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_tswrent') || count($user->getAuthorisedCategories('com_tswrent', 'core.create')) > 0) {
	$displayData['createURL'] = 'index.php?option=com_tswrent&task=customer.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
