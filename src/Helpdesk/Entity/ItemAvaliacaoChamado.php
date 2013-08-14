<?php

namespace Helpdesk\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\Entity 
 * @ORM\Table(name="ItemAvaliacaoChamado")
 * */
class ItemAvaliacaoChamado
{
    /**
     * @Annotation\AllowEmpty(true)
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\Id 
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue
     * @ORM\OneToMany(targetEntity="Helpdesk\Entity\Chamado", mappedBy="categoriachamado_fk")
     */
    public $iditemavaliacaochamado;
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     * @Annotation\Options({"label":"Item de avaliação : "})
     * @ORM\Column(type="string")
     */
    public $itemavaliacaonome;
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Options({"label":"Description: "})
     * @ORM\Column(type="text")
     */
    public $descricao;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @ORM\ManyToOne(targetEntity="Helpdesk\Entity\Setores")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="setor_fk", referencedColumnName="idsetor")
     * })
     * 
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
  
    public function getIditemavaliacaochamado() {
        return $this->iditemavaliacaochamado;
    }

    public function setIditemavaliacaochamado($iditemavaliacaochamado) {
        $this->iditemavaliacaochamado = $iditemavaliacaochamado;
    }

    public function getItemavaliacaonome() {
        return $this->itemavaliacaonome;
    }

    public function setItemavaliacaonome($itemavaliacaonome) {
        $this->itemavaliacaonome = $itemavaliacaonome;
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
      $this->setItemavaliacaonome($data['itemavaliacaonome']);
        $this->setDescricao($data['descricao']);
        $this->setIditemavaliacaochamado($data['iditemavaliacaochamado']);
        $this->setSetor_fk($data['setor_fk']);
    }

}
