<?php

namespace Helpdesk;

return array(
    'controllers' => array(
        'invokables' => array(
            'Helpdesk\Controller\Index' => 'Helpdesk\Controller\IndexController',
            'Helpdesk\Controller\StatusChamado' => 'Helpdesk\Controller\StatusChamadoController',
            'Helpdesk\Controller\Setor' => 'Helpdesk\Controller\SetorController',
            'Helpdesk\Controller\CategoriaChamado' => 'Helpdesk\Controller\CategoriaChamadoController',
            'Helpdesk\Controller\PrioridadeChamado' => 'Helpdesk\Controller\PrioridadeChamadoController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'helpdesk' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/helpdesk',
                    'defaults' => array(
                        'controller' => 'Helpdesk\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'open' => array(
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/open',
                            'defaults' => array(
                                'action' => 'store',
                            ),
                        ),
                    ),
                    'helpdesk-page' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/page[/:page]',
                            'constraints' => array(
                                'page' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                'page' => 1
                            ),
                        ),
                    ),
                    'chamado' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/chamado/:chamado',
                            'constraints' => array(
                                'chamado' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'chamado',
                                'chamado' => 0
                            ),
                        ),
                    ),
                    'fechar' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/fechar/chamado/:chamado',
                            'constraints' => array(
                                'chamado' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'close',
                                'chamado' => 0
                            ),
                        ),
                    ),
                    'changeprioridade' => array(
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => array(
                            'defaults' => array(
                               
                                'action' => 'changeprioridade',
                            ),
                            'route' => '/changeprioridade',
                            
                        ),
                    ),
                    'chamado-resposta' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/chamado/:id/resposta',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'resposta',
                                'id' => 0
                            ),
                        ),
                    ),
                )
            ),
            'setor' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/setor',
                    'defaults' => array(
                        'controller' => 'Helpdesk\Controller\Setor',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'store' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/store[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'store',
                            ),
                        ),
                    ),
                    'delete' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/delete[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'delete',
                            ),
                        ),
                    ),
                    'setor-page' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/page[/:page]',
                            'constraints' => array(
                                'page' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                'page' => 1
                            ),
                        ),
                    ),
                )
            ),
            'status-chamado' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/status-chamado',
                    'defaults' => array(
                        'controller' => 'Helpdesk\Controller\StatusChamado',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'store' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/store[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'store',
                            ),
                        ),
                    ),
                    'delete' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/delete[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'delete',
                            ),
                        ),
                    ),
                    'status-chamado-page' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/page[/:page]',
                            'constraints' => array(
                                'page' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                'page' => 1
                            ),
                        ),
                    ),
                )
            ),
            'prioridade-chamado' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/prioridade-chamado',
                    'defaults' => array(
                        'controller' => 'Helpdesk\Controller\PrioridadeChamado',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'store' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/store[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'store',
                            ),
                        ),
                    ),
                    'delete' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/delete[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'delete',
                            ),
                        ),
                    ),
                    'prioridade-page' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/page[/:page]',
                            'constraints' => array(
                                'page' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                'page' => 1
                            ),
                        ),
                    ),
                )
            ),
            'status-chamado' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/status-chamado',
                    'defaults' => array(
                        'controller' => 'Helpdesk\Controller\StatusChamado',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'store' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/store[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'store',
                            ),
                        ),
                    ),
                    'delete' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/delete[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'delete',
                            ),
                        ),
                    ),
                    'status-chamado-page' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/page[/:page]',
                            'constraints' => array(
                                'page' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                'page' => 1
                            ),
                        ),
                    ),
                )
            ),
            'categoria-chamado' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/categoria-chamado',
                    'defaults' => array(
                        'controller' => 'Helpdesk\Controller\categoriaChamado',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'store' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/store[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'store',
                            ),
                        ),
                    ),
                    'delete' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/delete[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'delete',
                            ),
                        ),
                    ),
                    'categoria-chamado-page' => array(
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/page[/:page]',
                            'constraints' => array(
                                'page' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                'page' => 1
                            ),
                        ),
                    ),
                )
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../../Base/view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../../Base/view/error/404.phtml',
            'error/index' => __DIR__ . '/../../Base/view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        ),
    ),
    'service_manager' => array(
        'factories' => array(
//            'Navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            "AuthenticationService" => ''
        ),
        'services' => array(
            'Auth' => new \Zend\Authentication\AuthenticationService()
        )
    ),
    'navigation' => array(
        // The DefaultNavigationFactory we configured in (1) uses 'default' as the sitemap key
        'default' => array(
            // And finally, here is where we define our page hierarchy
            'helpdesk' => array(
                'label' => 'Helpdesk',
                'route' => 'helpdesk',
//                'pages' => array(
//                    'abrir-chamado' => array(
//                        'label' => 'Abrir chamado',
//                        'route' => 'helpdesk/open',
//                    )
//                )
            ),
        ),
    ),
);
