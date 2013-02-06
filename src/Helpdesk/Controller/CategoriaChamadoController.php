<?php

namespace Helpdesk\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Helpdesk\Entity\CategoriaChamado;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

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

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $all = $entityManager->getRepository('Helpdesk\Entity\CategoriaChamado')->findAll();

        $paginator = new Paginator(new ArrayAdapter($all));
        $paginator->setDefaultItemCountPerPage(4);

        $messages = $this->flashMessenger()->getMessages();
        $page = (int) $this->params()->fromRoute('page', 1);
        if ($page)
            $paginator->setCurrentPageNumber($page);

        $view->setVariable('paginator', $paginator);
        $view->setVariable('messages', $messages);
        $view->setVariable('page', $page);

        return $view;
    }

    public function storeAction() {
        $setores = new CategoriaChamado();
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $setoreent = $entityManager->getRepository('Helpdesk\Entity\Setores')->findAll();
        $seterent = array();


        foreach ($setoreent as $sector) {
            $seterent[$sector->getIdsetor()] = $sector->getSetor();
        }
        $id = $this->params()->fromRoute('id');
        $anf = new AnnotationBuilder($this->getEntityManager());
        $form = $anf->createForm($setores);
        $form->get('setor_fk')->setEmptyOption('Choice a Sector')->setValueOptions($seterent);
        $form->setAttribute('action', '/categoria-chamado/store');

        if ($id) {
            $setor = $this->getEntityManager()->find('Helpdesk\Entity\CategoriaChamado', $id);

            $setor->setSubmit('Enviar');
            $form->bind($setor);
        }

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $data = $form->getData();


                if (!$data['idcategoriachamado']) {
                    $setor = $this->getEntityManager()->find('Helpdesk\Entity\Setores', $data['setor_fk']);
                    $data['setor_fk'] = $setor;
                    $setores->populate($data);
                    $this->getEntityManager()->persist($setores);
                    $this->getEntityManager()->flush();
                } else {

                    $setor = $this->getEntityManager()->find('Helpdesk\Entity\Setores', $data['setor_fk']);
                    $data['setor_fk'] = $setor;
                    $setores->populate($data);
                    $this->getEntityManager()->merge($setores);
                    $this->getEntityManager()->flush();
                }
                $this->flashMessenger()->addMessage('The Data are registred.');
                $this->redirect()->toRoute('categoria-chamado');
            }
        }


        return array('form' => $form);
    }

    public function deleteAction() {
        $setores = new CategoriaChamado();
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
            return $this->redirect()->toRoute('categoria-chamado');
        }


        return array('form' => $form, 'setor' => $setor);
    }

}
