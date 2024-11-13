<?php 
namespace HIEQueryUI;
class Navbar extends Widget implements onClick{
    protected $links;
    protected $childs = array();
    protected $active;
    protected $viewport;
    
    public function add($label,$target,$icon=false) {
        $it = new NavItem();
        $it->label = $label;
        $it->target = $target;
        $it->icon = $icon;
        $it->addClickListener($this);
        $this->childs[] = $it;
    }
    
    public function show()  {
        $this->displayed();
        $ht = '<ul id="'.$this->id.'" class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">';
        foreach ($this->childs as $c) {
            if($this->active == $c) {
                $c->active = true;
            } else {
                $c->active = false;
            }
            $ht .= $c->show();
        }
        $ht .= '</ul>';
        return $ht;
    }
    
    public function onClick(Widget $w) {
        $this->active = $w;
        if($this->viewport) {
            $this->viewport->set($w->target);
        }
        $this->changed();
    }
    
}