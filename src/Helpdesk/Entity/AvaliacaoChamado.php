<?php

namespace Helpdesk\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\Entity 
 * @ORM\Table(name="AvaliacaoChamado")
 * */
class AvaliacaoChamado {

    /**
     * @Annotation\AllowEmpty(true)
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\Id 
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue
     * @ORM\OneToMany(targetEntity="Helpdesk\Entity\Chamado", mappedBy="categoriachamado_fk")
     */
    public $idavaliacaochamado;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     * @Annotation\Options({"label":"Item de avaliação : "})
     * @ORM\Column(type="string")
     */
    public $nome;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     * @Annotation\Options({"label":"Item de avaliação : "})
     * @ORM\Column(type="datetime")
     */
    public $data;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Options({"label":"Description: "})
     * @ORM\Column(type="integer")
     */
    public $nota;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @ORM\ManyToOne(targetEntity="Helpdesk\Entity\ItemAvaliacaoChamado")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="itemavaliacao", referencedColumnName="iditemavaliacaochamado")
     * })
     * 
     */
    public $itemavaliacao;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @ORM\ManyToOne(targetEntity="Helpdesk\Entity\Chamado")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="chamado", referencedColumnName="idchamado")
     * })
     * 
     */
    public $chamado;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Enviar","class":"btn btn-success"})
     */
    public $submit;
    private $entityManager = null;

    public function getEntityManager() {
        return $this->entityManager;
    }

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function __construct() {
        $this->chamado = new ArrayCollection();
        $this->itemavaliacao = new ArrayCollection();
    }

    public function getIdavaliacaochamado() {
        return $this->idavaliacaochamado;
    }

    public function setIdavaliacaochamado($idavaliacaochamado) {
        $this->idavaliacaochamado = $idavaliacaochamado;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function getNota() {
        return $this->nota;
    }

    public function setNota($nota) {
        $this->nota = $nota;
    }

    public function getItemavaliacao() {
        return $this->itemavaliacao;
    }

    public function setItemavaliacao($itemavaliacao) {
        $this->itemavaliacao = $itemavaliacao;
    }

    public function getChamado() {
        return $this->chamado;
    }

    public function setChamado($chamado) {
        $this->chamado = $chamado;
    }

    public function getSubmit() {
        return $this->submit;
    }

    public function setSubmit($submit) {
        $this->submit = $submit;
    }

    public function populate(array $data) {
        $this->setChamado($data['chamado']);
        $this->setData($data['data']);
        $this->setIdavaliacaochamado($data['idavaliacaochamado']);
        $this->setItemavaliacao($data['itemavaliacao']);
        $this->setNome($data['nome']);
        $this->setNota($data['nota']);
    }

    public function store() {
        if (!$this->getIdavaliacaochamado()) {
            $this->getEntityManager()->persist($this);
             $this->getEntityManager()->flush();
        } else {
            $this->getEntityManager()->merge($this);
             $this->getEntityManager()->flush();
        }
        $this->getEntityManager()->flush();
    }

}
