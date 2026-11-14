<?php
namespace HIEQueryUI;
class Checkbox extends Button {
    public function __construct($title = '') { parent::__construct($title); }
    public function show() {
        $this->displayed();
        return '<input type="checkbox" id="'.Html::escape($this->id).'" aria-label="'.Html::escape($this->title).'" class="'.Html::escape(implode(' ', $this->classes)).'" onchange="eventcall(\''.$this->id.'\',\'onClick\')"'.($this->value ? ' checked' : '').($this->disabled ? ' disabled' : '').'>';
    }
}
