<?php
namespace HIEQueryUI;
class Navbar extends Widget implements onClick {
    protected $childs = array();
    protected $active;
    protected $viewport;
    public function add($label, $target, $icon = false) {
        $item = new NavItem(); $item->label = $label; $item->target = $target; $item->icon = $icon;
        $item->addClickListener($this); $this->childs[] = $item; $this->changed();
        return $item;
    }
    public function onClick(Widget $widget) {
        $this->active = $widget;
        if ($this->viewport) $this->viewport->set($widget->target);
        $this->changed();
    }
    public function show() {
        $this->displayed();
        $html = '<ul id="'.Html::escape($this->id).'" class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">';
        foreach ($this->childs as $child) { $child->active = $child === $this->active; $html .= $child->show(); }
        return $html.'</ul>';
    }
}
