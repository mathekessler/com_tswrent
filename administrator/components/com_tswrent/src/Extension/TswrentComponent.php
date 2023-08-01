<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
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
use Joomla\CMS\Tag\TagServiceInterface;
use Joomla\CMS\Tag\TagServiceTrait;
use TSWEB\Component\Tswrent\Administrator\Service\HTML\Tswrent;
use Joomla\Database\DatabaseInterface;
use Psr\Container\ContainerInterface;



\defined('JPATH_PLATFORM') or die;

/**
 * Component class for com_tswrent
 *
 */
class TswrentComponent extends MVCComponent implements
  BootableExtensionInterface,
  CategoryServiceInterface,
  RouterServiceInterface,
  TagServiceInterface
{
  use HTMLRegistryAwareTrait;
  use RouterServiceTrait;
  use CategoryServiceTrait, TagServiceTrait {
      CategoryServiceTrait::getTableNameForSection insteadof TagServiceTrait;
      CategoryServiceTrait::getStateColumnForSection insteadof TagServiceTrait;
  }


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
  */

  public function boot(ContainerInterface $container)
  {
    $tswrent = new Tswrent();
    $tswrent->setDatabase($container->get(DatabaseInterface::class));

    $this->getRegistry()->register('tswrent', $tswrent);

  }



    /**
     * Returns the table for the count items functions for the given section.
     *
     * @param   string  $section  The section
     *
     * @return  string|null
     *
     * @since   4.0.0
     */
    protected function getTableNameForSection(string $section = null)
    {
        return ($section === 'category' ? 'categories' : 'tswrent_product');
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