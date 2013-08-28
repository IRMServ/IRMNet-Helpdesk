<?php

namespace Helpdesk\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Helpdesk\Entity\Setores;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

class SetorController extends AbstractActionController {

    protected $em;

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function indexAction() {

        $view = new ViewModel();

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $all = $entityManager->getRepository('Helpdesk\Entity\Setores')->findAll();
       
        $paginator = new Paginator(new ArrayAdapter($all));
        $paginator->setDefaultItemCountPerPage(4);
        
        $messages = $this->flashMessenger()->getMessages();
        $page = (int) $this->params()->fromRoute('page',1);
        if ($page)
            $paginator->setCurrentPageNumber($page);

        $view->setVariable('paginator', $all);
        $view->setVariable('messages', $messages);
        $view->setVariable('page', $page);

        return $view;

    }

    public function storeAction() {
        $setores = new Setores();
        $id = $this->params()->fromRoute('id');
        $anf = new AnnotationBuilder($this->getEntityManager());
        $form = $anf->createForm($setores);
        $form->setAttribute('action', '/setor/store');

        if ($id) {
            $setor = $this->getEntityManager()->find('Helpdesk\Entity\Setores', $id);

            $setor->setSubmit('Enviar');
            $form->bind($setor);
        }

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                if (!$data['idsetor']) {
                    $setores->populate($data);
                    $this->getEntityManager()->persist($setores);
                    $this->getEntityManager()->flush();
                } else {
                    $setores->populate($data);
                   
                    $this->getEntityManager()->merge($setores);
                    $this->getEntityManager()->flush();
                }
                $this->flashMessenger()->addMessage('The Data are registred.');
                $this->redirect()->toRoute('setor');
            }
        }


        return array('form' => $form);
    }

    public function deleteAction() {
        $setores = new Setores();
        $id = $this->params()->fromRoute('id');
        $anf = new AnnotationBuilder($this->getEntityManager());
        $form = $anf->createForm($setores);
        $form->setAttribute('action', '/setor/store');

        if ($id) {
            $setor = $this->getEntityManager()->find('Helpdesk\Entity\Setores', $id);
            $setor->setSubmit('Enviar');
            $form->bind($setor);
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $setor = $this->getEntityManager()->find('Helpdesk\Entity\Setores', $id);
                $this->getEntityManager()->remove($setor);
                $this->getEntityManager()->flush();
            }
            $this->flashMessenger()->addMessage('The Data are removed.');
            return $this->redirect()->toRoute('setor');
        }


        return array('form' => $form, 'setor' => $setor);
    }

}
