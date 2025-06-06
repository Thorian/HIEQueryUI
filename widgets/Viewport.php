<?php
namespace HIEQueryUI;
class Viewport extends Widget {
    public $target;
    public function set(Widget $widget) { $this->target = $widget; $this->changed(); }
    public function show() {
        $this->displayed();
        return '<div id="'.Html::escape($this->id).'">'.($this->target ? $this->target->show() : '').'</div>';
    }
}
