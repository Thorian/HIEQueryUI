<?php
namespace HIEQueryUI;

class Checkbox extends Button {
    public function __construct() {
        parent::__construct();
        //$this->classes[] = "form-check-input";
        
    }
    public function show() {
        $this->displayed();
        $ht = '<input type="checkbox" id="'.$this->id.'" class="'.implode(" ",$this->classes).'" onclick="eventcall(\''.$this->id.'\',\'onClick\',\'\')"'.($this->style?' style="'.$this->style.'"':'').' '.($this->value?"checked":"").' style="display:inline-block;">';
        return $ht;
    }
}