<?php
namespace HIEQueryUI;

class Input extends InteractableWidget  {
    protected $title;
    protected $type="text";
    protected $autofocus = false;
    
    public function __construct($title="") {
        parent::__construct();
        $this->title = $title;
        $this->classes[] = "form-control";
        
    }
    
    public function show() {
        $this->displayed();
        $ht = '<input type="'.$this->type.'" id="'.$this->id.'" value="'.$this->value.'" onkeyup="deventcall(\''.$this->id.'\',\'onChange\')" class="'.implode(" ",$this->classes).'"'.($this->autofocus?" autofocus":"").'>';
        return $ht;
    }
}