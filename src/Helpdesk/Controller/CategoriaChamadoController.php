<?php

namespace Helpdesk\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Helpdesk\Entity\CategoriaChamado;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Debug\Debug;

class CategoriaChamadoController extends AbstractActionController {

    protected $em;

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function indexAction() {

        $view = new ViewModel();
        $setor = $this->params()->fromRoute('setor');
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $all = $entityManager->getRepository('Helpdesk\Entity\CategoriaChamado')->findBy(array('setor_fk' => $setor));

        $paginator = new Paginator(new ArrayAdapter($all));
        $paginator->setDefaultItemCountPerPage(4);

        $messages = $this->flashMessenger()->getMessages();
        $page = (int) $this->params()->fromRoute('page', 1);
        if ($page)
            $paginator->setCurrentPageNumber($page);

        $view->setVariable('paginator', $all);
        $view->setVariable('messages', $messages);
        $view->setVariable('page', $page);
        $view->setVariable('setor', $setor);

        return $view;
    }

    public function storeAction() {
        $setores = new CategoriaChamado();
        $setorid = $this->params()->fromRoute('setor');
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $setoreent = $entityManager->getRepository('Helpdesk\Entity\Setores')->find($setorid);

        $id = $this->params()->fromRoute('id');
        $anf = new AnnotationBuilder($this->getEntityManager());
        $form = $anf->createForm($setores);
        $form->get('setor_fk')->setEmptyOpTion('Escolha o setor')->setValueOptions($this->getServiceLocator()->get('SetoresPair'))->setValue($setoreent->getIdsetor());



        if ($id) {
            $setor = $this->getEntityManager()->find('Helpdesk\Entity\CategoriaChamado', $id);

            $setor->setSubmit('Enviar');
            $form->bind($setor);
        }

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $data = (array)$form->getData();


                if (!$data['idcategoriachamado']) {

                    $data['setor_fk'] = $setoreent;
                    $setores->populate($data);
                    $this->getEntityManager()->persist($setores);
                    $this->getEntityManager()->flush();
                } else {

                    $setor = $this->getEntityManager()->find('Helpdesk\Entity\Setores', $data['setor_fk']);
                    $data['setor_fk'] = $setoreent;
                    $setores->populate($data);
                    $this->getEntityManager()->merge($setores);
                    $this->getEntityManager()->flush();
                }
                $this->flashMessenger()->addMessage('The Data are registred.');
                $this->redirect()->toRoute('categoria-chamado', array('setor' => $setorid));
            }
        }


        return array('form' => $form);
    }

    public function deleteAction() {
        $setores = new CategoriaChamado();
        $setorid = $this->params()->fromRoute('setor');
        $setoreent = $this->getEntityManager()->getRepository('Helpdesk\Entity\Setores')->find($setorid);
        $id = $this->params()->fromRoute('id');
        $anf = new AnnotationBuilder($this->getEntityManager());
        $form = $anf->createForm($setores);
        $form->setAttribute('action', '/categoria-chamado/store');

        if ($id) {
            $setor = $this->getEntityManager()->find('Helpdesk\Entity\CategoriaChamado', $id);
            $setor->setSubmit('Enviar');
            $form->bind($setor);
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $setor = $this->getEntityManager()->find('Helpdesk\Entity\CategoriaChamado', $id);
                $this->getEntityManager()->remove($setor);
                $this->getEntityManager()->flush();
            }
            $this->flashMessenger()->addMessage('The Data are removed.');
             $this->redirect()->toRoute('categoria-chamado', array('setor' => $setorid));
        }


        return array('form' => $form, 'setor' => $setor);
    }

}
