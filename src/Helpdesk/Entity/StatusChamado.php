<?php

namespace Helpdesk\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\Entity 
 * @ORM\Table(name="StatusChamado")
 * */
class StatusChamado {

    /**
     * @Annotation\AllowEmpty(true)
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\Id 
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue
     * @ORM\OneToMany(targetEntity="Helpdesk\Entity\Chamado", mappedBy="statuschamado_fk")
     */
    public $idstatus;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     * @Annotation\Options({"label":"Status name : "})
     * @ORM\Column(type="string")
     */
    public $status;
    

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

    public function getIdstatus() {
        return $this->idstatus;
    }

    public function setIdstatus($idstatus) {
        $this->idstatus = (int) $idstatus;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function populate(array $data) {
        $this->setDescricao($data['descricao']);
        $this->setIdstatus($data['idstatus']);
        $this->setStatus($data['status']);
       
    }
    public function getSubmit() {
        return $this->submit;
    }

    public function setSubmit($submit) {
        $this->submit = $submit;
    }


}
