<?php
namespace HIEQueryUI;
class Input extends InteractableWidget {
    protected $title;
    protected $type = 'text';
    protected $autofocus = false;
    public function __construct($title = '') {
        parent::__construct(); $this->title = $title; $this->classes = array('form-control');
    }
    public function show() {
        $this->displayed();
        return '<input id="'.Html::escape($this->id).'" type="'.Html::escape($this->type).'" value="'.Html::escape($this->value).'" aria-label="'.Html::escape($this->title).'" class="'.Html::escape(implode(' ', $this->classes)).'" oninput="deventcall(\''.$this->id.'\',\'onChange\',event)" onkeydown="if(event.key===\'Enter\'){event.preventDefault();deventcall(\''.$this->id.'\',\'onEnter\',event)}"'.($this->autofocus ? ' autofocus' : '').'>';
    }
}
