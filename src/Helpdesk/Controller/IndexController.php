<?php

namespace Helpdesk\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Helpdesk\Entity\Chamado;
use Helpdesk\Entity\RespostaChamado;
use Helpdesk\Entity\PrioridadeChamado;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Zend\Filter\File\Rename;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Debug\Debug;
use \DateTime;
use MailService\Service\MailService as Mail;

class IndexController extends AbstractActionController {

    protected $em;

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function indexAction() {
        
        
        $view = new ViewModel;
        $setor = (int) $this->params()->fromRoute('setor', 1);
        $chamado = $this->getEntityManager()->getRepository('Helpdesk\Entity\Chamado')->findBy(array('setor_destino_fk' => $setor), array('idchamado' => 'desc'));
        $paginator = new Paginator(new ArrayAdapter($chamado));
        $paginator->setDefaultItemCountPerPage(20);
        $this->layout()->user = $this->getServiceLocator()->get('Auth')->hasIdentity();
        $messages = $this->flashMessenger()->getMessages();
        $page = (int) $this->params()->fromRoute('page', 1);
        if ($page)
            $paginator->setCurrentPageNumber($page);

        $view->setVariable('paginator', $paginator);
        $view->setVariable('messages', $messages);
        $view->setVariable('page', $page);
        $view->setVariable('setor', $setor);
          $view->setVariable('date', new Datetime());

        return $view;
    }

    public function chamadoAction() {
        $authservice = $this->getServiceLocator()->get('Auth');

        $this->layout()->user = $authservice->hasIdentity();
        $setor = $this->params()->fromRoute('setor');

        $user = $authservice->getStorage()->read();


        $view = new ViewModel;
        $id = $this->params()->fromRoute('chamado');

        $store = $authservice->getStorage()->read();

        $chamado = $this->getEntityManager()->find('Helpdesk\Entity\Chamado', $id);
        $resposta = $this->getEntityManager()->getRepository('Helpdesk\Entity\RespostaChamado')->findBy(array('chamado_fk' => $id));
        $prioridades = $this->getEntityManager()->getRepository('Helpdesk\Entity\PrioridadeChamado')->findAll();
        $prioridadelista = array();
        foreach ($prioridades as $p) {
            $prioridadelista[] = $p->prioridade;
        }
        $prioridadelista = implode(',', $prioridadelista);
        $view->setVariable('chamado', $chamado);
        $view->setVariable('setor', $setor);

        $view->setVariable('resposta', $resposta);
        $view->setVariable('store', $store);
        $view->setVariable('prioridades', $prioridadelista);
        $view->setVariable('id', $id);
        $view->setVariable('member', $user['departamento']);

        return $view;
    }

    public function storeAction() {
        $this->layout()->user = $this->getServiceLocator()->get('Auth')->hasIdentity();
        $anf = new AnnotationBuilder($this->getEntityManager());
        $chamado = new Chamado();
        $setor = $this->params()->fromRoute('setor');
        $form = $anf->createForm($chamado);

        $setor = $this->getEntityManager()->find('Helpdesk\Entity\Setores', $setor);
        $categoriachamado = $this->getEntityManager()->getRepository('Helpdesk\Entity\CategoriaChamado')->findAll();
        $priority = $this->getEntityManager()->getRepository('Helpdesk\Entity\PrioridadeChamado')->findAll();
        $chamado->setSetor_destino_fk($setor);
        $categorias = array();

        $prioridades = array();
        $form->setAttribute('action', "/helpdesk/{$setor->getIdsetor()}/open");
        $form->setAttribute('enctype', 'multipart/form-data');

        foreach ($categoriachamado as $cc) {
            $categorias[$cc->getIdcategoriachamado()] = $cc->getCategorianome();
        }
        foreach ($priority as $p) {
            $prioridades[$p->getIdprioridade()] = $p->getPrioridade();
        }

        $form->get('categoriachamado')->setEmptyOption('Escolha uma Categoria')->setValueOptions($categorias);
        $form->get('prioridade_fk')->setEmptyOption('Escolha a prioridade')->setValueOptions($prioridades);
        if ($this->getRequest()->isPost()) {

            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {

                $data = $form->getData();
                $author = $this->getServiceLocator()->get('Auth')->getStorage()->read();
                $priority = $this->getEntityManager()->find('Helpdesk\Entity\PrioridadeChamado', $data['prioridade_fk']);
                $categoriachamado = $this->getEntityManager()->find('Helpdesk\Entity\CategoriaChamado', $data['categoriachamado']);
                $statuschamado = $this->getEntityManager()->getRepository('Helpdesk\Entity\StatusChamado')->findBy(array('status' => 'Aberto'));


                $data['prioridade_fk'] = $priority;
                $data['categoriachamado'] = $categoriachamado;
                $data['setor_destino_fk'] = $setor;
                
                $data['setor_origem_fk'] = $author['departamento'];
                $data['arquivo'] = '';
                $data['autor'] = $author['displayname'];
                $data['statuschamado_fk'] = $statuschamado[0];
                $chamado->setDatainicio();

                $File = $this->params()->fromFiles('arquivo');

                if ($File['size'] > 0) {
                    $data['arquivo'] = $File['name'];


                    $size = new Size(array('max' => 5 * 1024 * 1024));
                    $adapter = new \Zend\File\Transfer\Adapter\Http();

                    $dir = \dirname(__DIR__);
                    $ex = \explode('intranet', $dir);
                    $exten = \explode('.', $File['name']);
                    $destino = $ex[0] . 'intranet\public\files\\' . md5(uniqid()) . '.' . $exten[1];

                    $rename = new Rename($destino);

                    $extension = new Extension(array('gif', 'jpg', 'pdf', 'bmp', 'png'));
                    $adapter->addFilter($rename);
                    $adapter->addValidator($extension)
                            ->addValidator($size);

                    if (!$adapter->isValid()) {
                        $dataError = $adapter->getMessages();
                        $error = array();
                        foreach ($dataError as $key => $row) {
                            $error[] = $row;
                        }
                        $form->setMessages(array('arquivo' => $error));
                        return array('form' => $form);
                    } else {
                        $chamado->populate($data);
                        $dir = \dirname(__DIR__);
                        $ex = explode('intranet', $dir);
                        $destino = $ex[0] . 'intranet\public\files';
                        $adapter->setDestination($destino);
                        if ($adapter->receive()) {
                            $data['arquivo'] = str_replace('\\', '/', end(explode('public', $adapter->getFileName())));
                            $chamado->populate($data);
                        }
                    }
                }
                $chamado->populate($data);
                $this->getEntityManager()->persist($chamado);
                $this->getEntityManager()->flush();
                $this->flashMessenger()->addMessage('As informações foram registradas.');
                return $this->redirect()->toRoute('helpdesk', array('setor' => $setor->getIdsetor()));
            } else {
                Debug::dump($form->getMessages());
            }
        }


        return array('form' => $form);
    }

    public function respostaAction() {
        $anf = new AnnotationBuilder($this->getEntityManager());
        $this->layout()->user = $this->getServiceLocator()->get('Auth')->hasIdentity();
        $store = $this->getServiceLocator()->get('Auth')->getStorage()->read();
        $resposta = new RespostaChamado();
        $resposta->setRegistro(new DateTime());



        $id = $this->params()->fromRoute("id");
        $setor = $this->params()->fromRoute("setor");

        $form = $anf->createForm($resposta);
        $form->setAttribute('enctype', 'multipart/form-data');

        $form->get('chamado_fk')->setValue($id);


        if ($this->getRequest()->isPost()) {


            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $chamado = $this->getEntityManager()->find('Helpdesk\Entity\Chamado', $data['chamado_fk']);
                $data['autor'] = $store['displayname'];
                $data['chamado_fk'] = $chamado;
                $data['registro'] = $resposta->getRegistro();
                $File = $this->params()->fromFiles('arquivo');
                if ($File['size'] > 0) {
                    $data['arquivo'] = $File['name'];


                    $size = new Size(array('max' => 5 * 1024 * 1024));
                    $adapter = new \Zend\File\Transfer\Adapter\Http();

                    $dir = \dirname(__DIR__);
                    $ex = \explode('intranet', $dir);
                    $exten = \explode('.', $File['name']);
                    $destino = $ex[0] . 'intranet\public\files\\' . md5(uniqid()) . '.' . $exten[1];

                    $rename = new Rename($destino);

                    $extension = new Extension(array('gif', 'jpg', 'pdf', 'bmp', 'png'));
                    $adapter->addFilter($rename);
                    $adapter->addValidator($extension)
                            ->addValidator('Size', array('min' => 52200));

                    if (!$adapter->isValid()) {
                        $dataError = $adapter->getMessages();
                        $error = array();
                        foreach ($dataError as $key => $row) {
                            $error[] = $row;
                        }
                        $form->setMessages(array('arquivo' => $error));
                        return array('form' => $form);
                    } else {

                        $resposta->populate($data);
                        $dir = \dirname(__DIR__);
                        $ex = explode('intranet', $dir);
                        $destino = $ex[0] . 'intranet\public\files';

                        $adapter->setDestination($destino);

                        if ($adapter->receive()) {
                            $data['arquivo'] = str_replace('\\', '/', end(explode('public', $adapter->getFileName())));

                            $resposta->populate($data);
                        }
                    }
                }

                $resposta->populate($data);
                $this->getEntityManager()->persist($resposta);
                $this->getEntityManager()->flush();
                $this->redirect()->toRoute('helpdesk/chamado', array('chamado' => $id, 'setor' => $setor));
            }
        }
        return array('form' => $form, 'user' => $store);
    }

    public function changeprioridadeAction() {

        $view = new ViewModel();
        $view->setTerminal(true)->setTemplate('helpdesk/index/changeprioridade');
        $this->getServiceLocator()->get('viewrenderer')->render($view);
        $post = $this->getRequest()->getPost();

        if (strtolower($post['setor']) == strtolower($post['member'])) {

            $p = new PrioridadeChamado();
            $statuschamado = $this->getEntityManager()->getRepository('Helpdesk\Entity\PrioridadeChamado')->findBy(array('prioridade' => $post['update_value']));
            $chamado = $this->getEntityManager()->find('Helpdesk\Entity\Chamado', $post['chamado']);

            $p->populate((array) $statuschamado[0]);
            $chamado->setPrioridade_fk($statuschamado[0]);
            $chamado->setPrevisao();
            $this->getEntityManager()->merge($chamado);
            $this->getEntityManager()->flush();

            $array['value'] = $post['update_value'];
            $array['previsao'] = $chamado->getPrevisao();
            $result = new JsonModel($array);
            return $result;
        } else {

            $array['value'] = $post['original_value'];
            $result = new JsonModel($array);
            return $result;
        }
    }

    public function closeAction() {
        $id = $this->params()->fromRoute('chamado');
        $chamado = $this->getEntityManager()->find('Helpdesk\Entity\Chamado', $id);
        $setor = $this->params()->fromRoute("setor");
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $statuschamado = $this->getEntityManager()->getRepository('Helpdesk\Entity\StatusChamado')->findBy(array('status' => 'Fechado'));
            $chamado->setDatafim();
            $chamado->setStatuschamado_fk($statuschamado[0]);
            $chamado->setMotivo($post['motivo']);
            $chamado->setNota($post['nota']);
            $this->getEntityManager()->merge($chamado);
            $this->getEntityManager()->flush();

            return $this->redirect()->toRoute('helpdesk', array('setor' => $post['setor']));
        }
        return new ViewModel(array('chamado' => $chamado, 'setor' => $setor));
    }

    public function mailAction() {
        $mail = new Mail($this->getServiceLocator());
        $mail->addFrom('webmaster@irmserv.com.br')->addTo('igor.carvalho@irmserv.com.br')->setSubject('teste mail service')->setBody('email de teste do mail service');
        $mail->send();
    }

}