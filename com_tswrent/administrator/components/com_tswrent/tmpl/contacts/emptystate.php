<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_TSWRENT_CONTACT',
	'formURL' => 'index.php?option=com_tswrent',
	'helpURL' => 'https://github.com/astridx/boilerplate#readme',
	'icon' => 'icon-copy',
];

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_tswrent') || count($user->getAuthorisedCategories('com_tswrent', 'core.create')) > 0) {
	$displayData['createURL'] = 'index.php?option=com_tswrent&task=contact.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
