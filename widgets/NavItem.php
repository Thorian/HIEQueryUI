<?php 
namespace HIEQueryUI;
class NavItem extends InteractableWidget {
    protected $label;
    protected $icon;
    protected $target;
    public $active = false;
    
    public function show() {
        $this->displayed();
        $ht = '<li id="'.$this->id.'" class="nav-item';
        
        if(is_a($this->target,"HIEQueryUI\Navbar")) {
            $ht .= ' has-treeview';
            $ht .='">';
            $ht .= '<a class="nav-link';
            if($this->active) {
                $ht .= ' active';
            }
            $ht .= '" href="#">';
        } else {
            $ht .='">';
            $ht .= '<a type="button" class="nav-link';
            if($this->active) {
                $ht .= ' active';
            }
            $ht .='" onclick="eventcall(\''.$this->id.'\',\'onClick\')">';
        }
        
        if($this->icon) {
            $ht .= '<i class="nav-icon fas '.$this->icon.'"></i>';
        }
        
        $ht .= '<p>'._($this->label);
        if(is_a($this->target,"HIEQueryUI\Navbar")) {
            $ht .= '<i class="right fas fa-angle-left"></i>';
        }
        $ht .= '</p>';
        
        if(is_a($this->target,"HIEQueryUI\Navbar")) {
            $ht .= '</a>';
            $ht .= str_replace("nav nav-pills nav-sidebar flex-column","nav nav-treeview",$this->target->show());
        } else {
            $ht .= '</a>';
        }
        $ht .= '</li>';
        return $ht;
    }
    
    
}