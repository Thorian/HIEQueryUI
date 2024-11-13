<?php 
namespace HIEQueryUI;

class Button extends InteractableWidget  {
    protected $title;
    protected $style;
    
    public function __construct($title="") {
        parent::__construct();
        $this->title = $title;
        $this->classes[] = "btn";
        $this->classes[] = "btn-block";
        $this->classes[] = "btn-default";
        
    }
    
    public function show() {
        $this->displayed();
        $ht = '<button type="button" id="'.$this->id.'" class="'.implode(" ",$this->classes).'" onclick="eventcall(\''.$this->id.'\',\'onClick\',\'\')"'.($this->style?' style="'.$this->style.'"':'').'>'.$this->title.'</button>';
        return $ht;
    }
}