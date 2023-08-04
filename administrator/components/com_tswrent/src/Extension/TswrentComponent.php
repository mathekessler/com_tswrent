<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Extension;

use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use TSWEB\Component\Tswrent\Administrator\Service\HTML\AdministratorService;
use Joomla\CMS\Helper\ContentHelper;
use Psr\Container\ContainerInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Component class for com_tswrent
 *
 *  @since   __BUMP_VERSION__
 */
class TswrentComponent extends MVCComponent implements BootableExtensionInterface, CategoryServiceInterface, RouterServiceInterface
{
	use CategoryServiceTrait;
	use HTMLRegistryAwareTrait;
	use RouterServiceTrait;


  /**
    * Booting the extension. This is the function to set up the environment of the extension like
    * registering new class loaders, etc.
     *
     * If required, some initial set up can be done from services of the container, eg.
     * registering HTML services.
     *
     * @param   ContainerInterface  $container  The container
     *
     * @return  void
     *
     *  @since   __BUMP_VERSION__
  */

  public function boot(ContainerInterface $container)
  {

    $this->getRegistry()->register('tswrentadministrator', new AdministratorService);

  }



    /**
     * Returns the table for the count items functions for the given section.
     *
     * @param   string  $section  The section
     *
     * @return  string|null
     *
     *  @since   __BUMP_VERSION__
     */
    protected function getTableNameForSection(string $section = null)
    {
        return ($section === 'category' ? 'categories' : 'tswrent_products');
    }

    /**
     * Returns the state column for the count items functions for the given section.
     *
     * @param   string  $section  The section
     *
     * @return  string|null
     *
     * @since   4.0.0
     */
    protected function getStateColumnForSection(string $section = null)
    {
        return 'published';
    }


	
}