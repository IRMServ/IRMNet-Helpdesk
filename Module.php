<?php

namespace Helpdesk;

use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\Tools\SchemaTool;

class Module {

    public function onBootstrap(MvcEvent $e) {
        $application = $e->getApplication();
        $services = $application->getServiceManager();
        $this->initDatabase($services);
    }

    protected function initDatabase(ServiceLocatorInterface $services) {
        $em = $services->get('doctrine.entitymanager.orm_default');

        $dir = __DIR__ . '/src/' . __NAMESPACE__ . '/Entity/*.php';
        $classes = array();

        foreach (glob($dir) as $file) {
         
            $filename = end(explode('/', $file));
               $lock = 'lock/'.$filename . '.lock';
            if (!file_exists($lock)) {
                include($file);
                $entity = str_replace('.php', '', $file);
                $ex = end(explode('/', $entity));

                $newentity = __NAMESPACE__ . '\Entity\\' . $ex;
                $obj = new $newentity;
                $classes[] = $em->getClassMetadata($newentity);
                file_put_contents($lock, 'lock');
            }
        }
        $tool = new SchemaTool($em);
        $tool->createSchema($classes);
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

}
