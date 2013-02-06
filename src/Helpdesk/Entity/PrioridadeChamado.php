<?php

namespace Helpdesk\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\Entity 
 * @ORM\Table(name="PrioridadeChamado")
 * */
class PrioridadeChamado {

    /**
     * @Annotation\AllowEmpty(true)
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\Id 
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue
     * @ORM\OneToMany(targetEntity="Helpdesk\Entity\Chamado", mappedBy="prioridade_fk")
     */
    public $idprioridade;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     * @Annotation\Options({"label":"Priority name : "})
     * @ORM\Column(type="string")
     */
    public $prioridade;
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     * @Annotation\Options({"label":"Days for job : "})
     * @ORM\Column(type="string")
     */
    public $dias;
    

        /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\AllowEmpty(true)
     * @Annotation\Options({"label":"Description: "})
     * @ORM\Column(type="text")
     */
    public $descricao;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Enviar","class":"btn btn-success"})
     */
    public $submit;

    

    public function populate(array $data) {
        //$this->setDescricao($data['descricao']);
        $this->setIdprioridade($data['idprioridade']);
        $this->setDias($data['dias']);
        $this->setPrioridade($data['prioridade']);
       
    }
    
    public function getIdprioridade() {
        return $this->idprioridade;
    }

    public function setIdprioridade($idprioridade) {
        $this->idprioridade = $idprioridade;
    }

    public function getPrioridade() {
        return $this->prioridade;
    }

    public function setPrioridade($prioridade) {
        $this->prioridade = $prioridade;
    }

    public function getDias() {
        return $this->dias;
    }

    public function setDias($dias) {
        $this->dias = $dias;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function getSubmit() {
        return $this->submit;
    }

    public function setSubmit($submit) {
        $this->submit = $submit;
    }



}
