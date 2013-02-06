<?php

namespace Helpdesk\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\Entity 
 * @ORM\Table(name="Setores")
 * */
class Setores {

    /**
     * @Annotation\AllowEmpty(true)
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\Id 
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue
     * @ORM\OneToMany(targetEntity="Helpdesk\Entity\CategoriaChamado", mappedBy="setor_fk")
     * @ORM\OneToMany(targetEntity="Helpdesk\Entity\Chamado", mappedBy="setor_destino_fk")
     */
    public $idsetor;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     * @Annotation\Options({"label":"Setor : "})
     * @ORM\Column(type="string")
     */
    public $setor;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"Descrilção: "})
     * @Annotation\AllowEmpty(true)
     * @ORM\Column(type="text")
     */
    public $descricao;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Enviar","class":"btn btn-success","id":"submit"})
     */
    public $submit;

    public function getIdsetor() {
        return $this->idsetor;
    }

    public function setIdsetor($idsetor) {
        $this->idsetor = $idsetor;
    }

    public function getSetor() {
        return $this->setor;
    }

    public function setSetor($setor) {
        $this->setor = $setor;
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

    public function populate(array $data) {
        
        $this->setDescricao($data['descricao']);
        $this->setIdsetor($data['idsetor']);
        $this->setSetor($data['setor']);
    }

    public function toArray() {
        $prop = array_values(get_class_methods(get_class($this)));
        $data = array();
        foreach ($prop as $p => $v) {
            if (substr($v, 0, 3) == 'get') {
                $data[substr($v,3,strlen($v))] = call_user_func(array($this, $v));
            }
        }
        return $data;
    }

}
