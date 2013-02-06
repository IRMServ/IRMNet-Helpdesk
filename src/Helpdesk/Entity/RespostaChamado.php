<?php

namespace Helpdesk\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;
/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\Entity 
 * @ORM\Table(name="RespostaChamado")
 * */
class RespostaChamado {

    /**
     * @Annotation\AllowEmpty(true)
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\Id 
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue
     */
    public $idresposta;

    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     
     * @ORM\Column(type="datetime")
     */
    public $registro;
    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     
     * @ORM\Column(type="string")
     */
    public $autor;
    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})     
     * @ORM\ManyToOne(targetEntity="Helpdesk\Entity\Chamado", inversedBy="idchamado")
     * @ORM\JoinColumn(name="chamado_fk", referencedColumnName="idchamado")
     */
    public $chamado_fk;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"Resposta: "})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     * @ORM\Column(type="text")
     */
    public $resposta;
       /**
     * @Annotation\Type("Zend\Form\Element\File")
     * @Annotation\Options({"label":"Imagem: "})
     * @Annotation\AllowEmpty(true)
     * @ORM\Column(type="text",nullable=true)
     */
    public $arquivo;

    /**
     * @Annotation\AllowEmpty(true)
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Enviar","class":"btn btn-success","id":"submit"})
     */
    public $submit;

    public function populate(array $data) {
        
       $this->setAutor($data['autor']);
       $this->setChamado_fk($data['chamado_fk']);
       $this->setIdresposta($data['idresposta']);
       $this->setResposta($data['resposta']);      
       $this->setArquivo($data['arquivo']);
    }
    
    public function getArquivo() {
        return $this->arquivo;
    }

    public function setArquivo($arquivo) {
        $this->arquivo = $arquivo;
    }

        public function getIdresposta() {
        return $this->idresposta;
    }

    public function setIdresposta($idresposta) {
        $this->idresposta = $idresposta;
    }

    public function getRegistro() {
        return $this->registro->format('d/m/Y - H:i:s');
    }

    public function setRegistro($registro) {
        $this->registro = $registro;
    }

    public function getAutor() {
        return $this->autor;
    }

    public function setAutor($autor) {
        $this->autor = $autor;
    }

    public function getChamado_fk() {
        return $this->chamado_fk;
    }

    public function setChamado_fk($chamado_fk) {
        $this->chamado_fk = $chamado_fk;
    }

    public function getResposta() {
        return $this->resposta;
    }

    public function setResposta($resposta) {
        $this->resposta = $resposta;
    }

    public function getSubmit() {
        return $this->submit;
    }

    public function setSubmit($submit) {
        $this->submit = $submit;
    }

    public function __construct() {
        $this->chamado_fk = new ArrayCollection();
        $this->setRegistro(new DateTime());
    }


}
