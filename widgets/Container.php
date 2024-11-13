<?php 
namespace HIEQueryUI;
class Container extends Widget {
    protected $childs = array();
    
    public function add(Widget $child) {
        $this->childs[] = $child;
    }
    
    public function del(Widget $child) {
        foreach ($this->childs as $key => $c) {
            if($c == $child)
                unset($this->childs[$key]);
        }
    }
    
    public function show()  {
        $this->displayed();
        $ht = '<div id="'.$this->id.'">';
        foreach ($this->childs as $c) {
            $ht .= $c->show();
        }
        $ht .= '</div>';
        return $ht;
    }
}