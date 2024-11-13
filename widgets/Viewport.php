<?php
namespace HIEQueryUI;
class Viewport extends Widget {
    public $target;
    
    public function set(Widget $w) {
        $this->target = $w;
        $this->changed();
    }
    
    public function show() {
        $this->displayed();
        $ht = '<div id="'.$this->id.'">';
        
        if($this->target) {
            $ht .= $this->target->show();
        }
        
        $ht .= '</div>';
        return $ht;
    }
}