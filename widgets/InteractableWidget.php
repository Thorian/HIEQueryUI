<?php 
namespace HIEQueryUI;
class InteractableWidget extends Widget {
    protected $listeners = array();
    protected $value;
    public function addClickListener(onClick $c) {

        $this->listeners["onClick"][] = $c;
    }
    public function addChangeListener(onChange $c) {
        $this->listeners["onChange"][] = $c;
    }
    public function addTimeoutListener(onTimeout $c) {
        $this->listeners["onTimeout"][] = $c;
    }
    public function addEnterListener(OnEnter $c) {
        $this->listeners["onEnter"][] = $c;
    }
    
    public function onClick($data) {
        $this->value = base64_decode($data);
        if(!isset($this->listeners["onClick"])) return;
        foreach($this->listeners["onClick"] as $c) {
            $c->onClick($this);
        }
    }
    public function onEnter($data) {
        $this->value = base64_decode($data);
        if(!isset($this->listeners["onEnter"])) return;
        foreach($this->listeners["onEnter"] as $c) {
            $c->onEnter($this);
        }
    }
    public function onChange($data) {
        $this->value = base64_decode($data);
        if(!isset($this->listeners["onChange"])) return;
        foreach($this->listeners["onChange"] as $c) {
            $c->onChange($this);
        }
    }
    public function onTimeout($data) {
        if(!isset($this->listeners["onTimeout"])) return;
        foreach($this->listeners["onTimeout"] as $c) {
            $c->onTimeout($this);
        }
    }
}