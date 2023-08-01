<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_TSWRENT',
	'formURL'    => 'index.php?option=com_tswrent&view=products',
	'icon'       => 'icon-rss newsfeeds',
];

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_tswrent') || count($user->getAuthorisedCategories('com_tswrent', 'core.create')) > 0)
{
	$displayData['createURL'] = 'index.php?option=com_tswrent&task=product.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);