<?php

/**
 * @package     tswrent
 *
 * @copyright   (C)
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;


use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use TSWEB\Component\Tswrent\Administrator\Extension\TswrentComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;


/**
 * The banners service provider.
 *
 * @since  4.0.0
 */
return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function register(Container $container)
    {
        $componentNamespace = '\\TSWEB\\Component\\Tswrent';
        $container->registerServiceProvider(new CategoryFactory($componentNamespace));
        $container->registerServiceProvider(new MVCFactory($componentNamespace));
        $container->registerServiceProvider(new ComponentDispatcherFactory($componentNamespace));
        $container->registerServiceProvider(new RouterFactory($componentNamespace));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new TswrentComponent($container->get(ComponentDispatcherFactoryInterface::class));
                
				$component->setRegistry($container->get(Registry::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
                $component->setRouterFactory($container->get(RouterFactoryInterface::class));


                return $component;
            }
        );
    }
};
