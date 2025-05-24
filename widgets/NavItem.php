<?php
namespace HIEQueryUI;
class NavItem extends InteractableWidget {
    protected $label;
    protected $icon;
    protected $target;
    public $active = false;
    public function show() {
        $this->displayed();
        $nested = $this->target instanceof Navbar;
        $html = '<li id="'.Html::escape($this->id).'" class="nav-item'.($nested ? ' has-treeview' : '').'">';
        $html .= '<a href="#" class="nav-link'.($this->active ? ' active' : '').'"'.($nested ? '' : ' onclick="event.preventDefault();eventcall(\''.$this->id.'\',\'onClick\')"').'>';
        if ($this->icon) $html .= '<i aria-hidden="true" class="nav-icon '.Html::escape($this->icon).'"></i>';
        $html .= '<p>'.Html::content($this->label).($nested ? '<i class="right fas fa-angle-left"></i>' : '').'</p></a>';
        if ($nested) $html .= str_replace('nav nav-pills nav-sidebar flex-column', 'nav nav-treeview', $this->target->show());
        return $html.'</li>';
    }
}
