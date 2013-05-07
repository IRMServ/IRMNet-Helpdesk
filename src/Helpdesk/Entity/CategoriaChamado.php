<?php

namespace Helpdesk\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\Entity 
 * @ORM\Table(name="CategoriaChamado")
 * */
class CategoriaChamado
{
    /**
     * @Annotation\AllowEmpty(true)
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\Id 
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue
     * @ORM\OneToMany(targetEntity="Helpdesk\Entity\Chamado", mappedBy="categoriachamado_fk")
     */
    public $idcategoriachamado;
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     * @Annotation\Options({"label":"Category name : "})
     * @ORM\Column(type="string")
     */
    public $categorianome;
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Options({"label":"Description: "})
     * @ORM\Column(type="text")
     */
    public $descricao;
    
    /**
     *@Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\ManyToOne(targetEntity="Helpdesk\Entity\Setores", inversedBy="idsetor")
     * @ORM\JoinColumn(name="setor_fk", referencedColumnName="idsetor")
     */
    public $setor_fk;
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Enviar","class":"btn btn-success"})
     */
    public $submit;
    
    public function __construct() {
         $this->setor_fk = new ArrayCollection();
    }
  
    public function getIdcategoriachamado() {
        return $this->idcategoriachamado;
    }

    public function setIdcategoriachamado($idcategoriachamado) {
        $this->idcategoriachamado = $idcategoriachamado;
    }

    public function getCategorianome() {
        return $this->categorianome;
    }

    public function setCategorianome($categorianome) {
        $this->categorianome = $categorianome;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function getSetor_fk() {
        return $this->setor_fk;
    }

    public function setSetor_fk($setor_fk) {
        $this->setor_fk = $setor_fk;
    }

    public function getSubmit() {
        return $this->submit;
    }

    public function setSubmit($submit) {
        $this->submit = $submit;
    }
    
    public function populate(array $data)
    {
        $this->setCategorianome($data['categorianome']);
        $this->setDescricao($data['descricao']);
        $this->setIdcategoriachamado($data['idcategoriachamado']);
        $this->setSetor_fk($data['setor_fk']);
    }

}
