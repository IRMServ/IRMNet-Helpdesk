<?php

namespace Helpdesk\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use \DateTime;
use \DateInterval;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\Entity 
 * @ORM\Table(name="Chamado")
 * */
class Chamado {

    /**
     * @Annotation\AllowEmpty(true)
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\Id 
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue
     */
    public $idchamado;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     * @Annotation\Options({"label":"Título *: "})
     * @ORM\Column(type="string")
     */
    public $titulo;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     * @Annotation\Options({"label":"Descrição *: "})
     * @ORM\Column(type="text")
     */
    public $descricao;

    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\AllowEmpty(true)
     * @ORM\Column(type="datetime")
     */
    public $datainicio;

    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\AllowEmpty(true)
     * @ORM\Column(type="datetime",nullable=true)
     */
    public $datafim;

    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\AllowEmpty(true)
     * @ORM\Column(type="datetime")
     */
    public $previsao;

    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StripTags"}) 
     * @ORM\Column(type="string")
     */
    public $autor;

    /**
     * @Annotation\AllowEmpty(true)    
     * @Annotation\Type("Zend\Form\Element\File")
     * @Annotation\Options({"label":"Imagem: "})    
     * @ORM\Column(type="text",nullable=true)
     */
    public $arquivo;

    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\AllowEmpty(true)
     * @ORM\Column(type="string")
     */
    public $setor_origem_fk;
    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\AllowEmpty(true)
     * @ORM\Column(type="string")
     */
    public $motivo;
    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\AllowEmpty(true)
     * @ORM\Column(type="integer",length=2)
     */
    public $nota;
   

    
        /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\AllowEmpty(true)
     * @ORM\ManyToOne(targetEntity="Helpdesk\Entity\StatusChamado", inversedBy="idstatus")
     * @ORM\JoinColumn(name="statuschamado_fk", referencedColumnName="idstatus")
     */
    public $statuschamado_fk;

    /**
     * @Annotation\AllowEmpty(true)
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\ManyToOne(targetEntity="Helpdesk\Entity\Setores", inversedBy="idsetor")
     * @ORM\JoinColumn(name="setor_destino_fk", referencedColumnName="idsetor")
     */
    public $setor_destino_fk;

    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\ManyToOne(targetEntity="Helpdesk\Entity\PrioridadeChamado", inversedBy="idprioridade")
     * @ORM\JoinColumn(name="prioridade_fk", referencedColumnName="idprioridade")
     */
    public $prioridade_fk;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Options({"label":"Categoria *: "})
     * @ORM\ManyToOne(targetEntity="Helpdesk\Entity\CategoriaChamado", inversedBy="idcategoriachamado")
     * @ORM\JoinColumn(name="categoriachamado_fk", referencedColumnName="idcategoriachamado")
     * */
    public $categoriachamado;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Enviar","class":"btn btn-success"})
     */
    public $submit;

    public function getIdchamado() {
        return $this->idchamado;
    }

    public function setIdchamado($idchamado) {
        $this->idchamado = $idchamado;
    }

    public function getTitulo() {
        return $this->titulo;
    }

    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function getDatainicio() {        
        return $this->datainicio->format("d/m/Y H:i:s");
    }

    public function setDatainicio() {
        
            $this->datainicio = new DateTime();
        
    }

    public function getDatafim() {
       
        return  $this->datafim instanceof DateTime ? $this->datafim->format("d/m/Y H:i:s"):'';
    }

    public function setDatafim() {
        $this->datafim = new DateTime();
    }

    public function getAutor() {
        return $this->autor;
    }

    public function setAutor($autor) {
        $this->autor = $autor;
    }

    public function getArquivo() {
        return $this->arquivo;
    }

    public function setArquivo($arquivo) {
        $this->arquivo = $arquivo;
    }

    public function getSetor_origem_fk() {
        return $this->setor_origem_fk;
    }

    public function setSetor_origem_fk($setor_origem_fk) {
        $this->setor_origem_fk = $setor_origem_fk;
    }

    public function getStatuschamado_fk() {
        return $this->statuschamado_fk;
    }

    public function setStatuschamado_fk($statuschamado_fk) {
        $this->statuschamado_fk = $statuschamado_fk;
    }

    public function getSetor_destino_fk() {
        return $this->setor_destino_fk;
    }

    public function setSetor_destino_fk($setor_destino_fk) {
        $this->setor_destino_fk = $setor_destino_fk;
    }

    public function getSubmit() {
        return $this->submit;
    }

    public function setSubmit($submit) {
        $this->submit = $submit;
    }

    public function getCategoriachamado() {
        return $this->categoriachamado;
    }

    public function setCategoriachamado($categoriachamado) {
        $this->categoriachamado = $categoriachamado;
    }

    public function populate(array $data) {
        $this->setPrioridade_fk($data['prioridade_fk']);
        $this->setAutor($data['autor']);
        $this->setCategoriachamado($data['categoriachamado']);
        $this->setPrevisao();
        $this->setDatainicio(null);
        $this->setDescricao($data['descricao']);
        $this->setIdchamado($data['idchamado']);
        $this->setSetor_destino_fk($data['setor_destino_fk']);
        $this->setSetor_origem_fk($data['setor_origem_fk']);
        $this->setStatuschamado_fk($data['statuschamado_fk']);
        $this->setTitulo($data['titulo']);
        $this->setArquivo($data['arquivo']);
        $this->setMotivo($data['motivo']);
        $this->setNota($data['nota']);
    }

    public function getPrevisao() {
       
        return $this->previsao->format("d/m/Y H:i:s");
    }

    public function setPrevisao() {
       
            $data = new DateTime();
            $this->previsao = $data->add(new DateInterval("P{$this->prioridade_fk->getDias()}D"));
       
    }

    public function getPrioridade_fk() {
        return $this->prioridade_fk;
    }

    public function setPrioridade_fk($prioridade_fk) {
        $this->prioridade_fk = $prioridade_fk;
    }
    
     public function getMotivo() {
        return $this->motivo;
    }

    public function setMotivo($motivo) {
        $this->motivo = $motivo;
    }
 public function getNota() {
        return $this->nota;
    }

    public function setNota($nota) {
        $this->nota = $nota;
    }
}
