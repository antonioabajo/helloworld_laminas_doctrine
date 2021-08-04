<?php
namespace Album\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller.
 */
class AlbumControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        
        // Instantiate the controller and inject dependencies
        return new \Album\Controller\AlbumController($entityManager);
    }
}




