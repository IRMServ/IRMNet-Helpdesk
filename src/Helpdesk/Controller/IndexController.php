<?php

namespace Helpdesk\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Helpdesk\Entity\Chamado;
use Helpdesk\Entity\RespostaChamado;
use Helpdesk\Entity\PrioridadeChamado;
use Zend\Form\Annotation\AnnotationBuilder;
use Helpdesk\Entity\AvaliacaoChamado;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Zend\Filter\File\Rename;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Debug\Debug;
use \DateTime;
use MailService\Service\ServiceTemplate;
use MailService\Service\MailService;

class IndexController extends AbstractActionController {

    protected $em;

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function indexAction() {

        $this->layout()->user = $this->getServiceLocator()->get('Auth')->hasIdentity();
        $user = $this->getServiceLocator()->get('Auth')->getStorage()->read();
        $view = new ViewModel;
        $setor = (int) $this->params()->fromRoute('setor', 1);
        $chamado = $this->getEntityManager()->getRepository('Helpdesk\Entity\Chamado')->findBy(array('setor_destino_fk' => $setor, 'autor' => $user['displayname']), array('idchamado' => 'desc'));

        $paginator = new Paginator(new ArrayAdapter($chamado));

        $paginator->setDefaultItemCountPerPage(4);

        $messages = $this->flashMessenger()->getMessages();
        $page = (int) $this->params()->fromRoute('page', 1);
        if ($page)
            $paginator->setCurrentPageNumber($page);

        $view->setVariable('paginator', $chamado);
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
        $user = $this->getServiceLocator()->get('Auth')->getStorage()->read();
          $s = $this->getEntityManager()->getRepository('Helpdesk\Entity\StatusChamado')->findBy(array('status' => 'Fechado'));
        $chamados_abertos = $this->getEntityManager()->getRepository('Helpdesk\Entity\Chamado')->findBy(array('autor' => $user['displayname'],'statuschamado_fk'=>$s, 'nota' => 0));


        $setor = $this->params()->fromRoute('setor');
        $anf = new AnnotationBuilder($this->getEntityManager());
        $chamado = new Chamado();
        $setor = $this->params()->fromRoute('setor');
        $form = $anf->createForm($chamado);
        //print_r(get_class($form->get('categoriachamado')));
        if (count($chamados_abertos) == 0) {


            $setor = $this->getEntityManager()->find('Helpdesk\Entity\Setores', $setor);
            $categoriachamado = $this->getEntityManager()->getRepository('Helpdesk\Entity\CategoriaChamado')->findBy(array('setor_fk' => $setor->getIdsetor()),array('categorianome'=>'asc'));

            $prioridade = $this->getEntityManager()->getRepository('Helpdesk\Entity\PrioridadeChamado')->findBy(array('prioridade' => 'Normal'));
            $chamado->setSetor_destino_fk($setor);
            $categorias = array();


            $form->setAttribute('action', "/helpdesk/{$setor->getIdsetor()}/open");
            $form->setAttribute('enctype', 'multipart/form-data');

            foreach ($categoriachamado as $cc) {
                $categorias[$cc->getIdcategoriachamado()] = $cc->getCategorianome();
            }

            $author = $this->getServiceLocator()->get('Auth')->getStorage()->read();
            $form->get('categoriachamado')->setEmptyOption('Escolha uma Categoria')->setValueOptions($categorias);
            $form->get('prioridade_fk')->setValue($prioridade[0]->getIdprioridade());
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

                    
                    $mail = new MailService($this->getServiceLocator(),ServiceTemplate::HELPDESK_ABERTURA);
                    $mail->addFrom('webmaster@irmserv.com.br')
                            ->addTo($author['email'])
                            ->addCc($setor->getEmail())
                            
                            ->setSubject("[chamado aberto] {$chamado->getTitulo()}")
                            ->setBody(array('setor' => $setor->getIdsetor(), 'sujeito' => $author['displayname'], 'chamado' => $chamado->getIdchamado(), 'titulo' => $chamado->getTitulo(), 'conteudo' => $chamado->getDescricao()));

                    $mail->send();
                    $this->flashMessenger()->addMessage('As informações foram registradas.');
                    return $this->redirect()->toRoute('helpdesk', array('setor' => $setor->getIdsetor()));
                } 
            }


            return array('form' => $form);
        } else {
            return new ViewModel(array('chamados_abertos' => $chamados_abertos, 'setor' => $setor));
        }
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
        $setor = $this->getEntityManager()->find('Helpdesk\Entity\Setores', $setor);
        $chamado = $this->getEntityManager()->find('Helpdesk\Entity\Chamado', $id);
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
                $renderer = $this->getServiceLocator()->get('ViewRenderer');


                $mail = new MailService($this->getServiceLocator(),ServiceTemplate::HELPDESK_RESPOSTA);
                $mail->addFrom('webmaster@irmserv.com.br')
                        ->addTo($store['email'])
                        ->addCc($setor->getEmail())
                        //->addTo($setor->getEmail())
                        ->setSubject("[resposta chamado] {$chamado->getTitulo()}")
                        ->setBody(array('setor' => $setor->getIdsetor(), 'sujeito' => $store['displayname'], 'chamado' => $chamado->getIdchamado(), 'titulo' => $chamado->getTitulo(), 'conteudo' => $resposta->getResposta()));


                $mail->send();
                return $this->redirect()->toRoute('helpdesk', array('setor' => $setor->getIdsetor()));
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
        $setor = $this->getEntityManager()->find('Helpdesk\Entity\Setores', $setor);
        $store = $this->getServiceLocator()->get('Auth')->getStorage()->read();
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $statuschamado = $this->getEntityManager()->getRepository('Helpdesk\Entity\StatusChamado')->findBy(array('status' => 'Fechado'));
            $chamado->setDatafim();
            $chamado->setStatuschamado_fk($statuschamado[0]);
            $chamado->setMotivo('');
            $chamado->setNota(0);
            $this->getEntityManager()->merge($chamado);
            $this->getEntityManager()->flush();
            $renderer = $this->getServiceLocator()->get('ViewRenderer');

            

            $mail = new MailsERVICE($this->getServiceLocator(),ServiceTemplate::HELPDESK_FECHAMENTO);
            $mail->addFrom('webmaster@irmserv.com.br')
                    ->addTo($store['email'])
                    ->addCc($setor->getEmail())
                    //->addTo($setor->getEmail())
                    ->setSubject("[fechamento chamado] {$chamado->getTitulo()}")
                    ->setBody(array('setor' => $setor->getIdsetor(), 'sujeito' => $store['displayname'], 'chamado' => $chamado->getIdchamado(), 'titulo' => $chamado->getTitulo()));


            $mail->send();

            return $this->redirect()->toRoute('helpdesk', array('setor' => $post['setor']));
        }
        return new ViewModel(array('chamado' => $chamado, 'setor' => $setor));
    }

    public function avaliarAction() {
         $id = $this->params()->fromRoute('chamado');
        $chamado = $this->getEntityManager()->find('Helpdesk\Entity\Chamado', $id);
        $setor = $this->params()->fromRoute("setor");
        $setor = $this->getEntityManager()->find('Helpdesk\Entity\Setores', $setor);
        $itens = $this->getEntityManager()->getRepository('Helpdesk\Entity\ItemAvaliacaoChamado')->findBy(array('setor_fk' => $setor));
        $store = $this->getServiceLocator()->get('Auth')->getStorage()->read();
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
           
           
            $chamado = $this->getEntityManager()->find('Helpdesk\Entity\Chamado', $post['chamado']);
            foreach ($post as $key => $value) {
               
                if (strpos($key,'nota' )!== false) {
                    $chamado = $this->getEntityManager()->find('Helpdesk\Entity\Chamado', $post['chamado']);
                    $itemavaliacaoid = explode('_', $key);
                    $itemavaliacao = $this->getEntityManager()->find('Helpdesk\Entity\ItemAvaliacaoChamado', end($itemavaliacaoid));
                    $avaliacaochamado = new AvaliacaoChamado();
                    $avaliacaochamado->setEntityManager($this->getEntityManager());
                    $avaliacaochamado->setChamado($chamado);
                    $avaliacaochamado->setItemavaliacao($itemavaliacao);
                    $avaliacaochamado->setNota($value);
                    $avaliacaochamado->setData(new \DateTime('now'));
                    $avaliacaochamado->setNome($store['displayname']);
                   
                    $avaliacaochamado->store();
                }
            }


            $statuschamado = $this->getEntityManager()->getRepository('Helpdesk\Entity\StatusChamado')->findBy(array('status' => 'Fechado'));

            $chamado->setStatuschamado_fk($statuschamado[0]);
            $chamado->setMotivo($post['motivo']);

            $this->getEntityManager()->merge($chamado);
            $this->getEntityManager()->flush();


            return $this->redirect()->toRoute('helpdesk', array('setor' => $post['setor']));
        }
        return new ViewModel(array('chamado' => $chamado, 'setor' => $setor, 'items' => $itens));
    }

}