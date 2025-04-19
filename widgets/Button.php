<?php
namespace HIEQueryUI;
class Button extends InteractableWidget {
    protected $title;
    protected $style = '';
    public function __construct($title = '') {
        parent::__construct();
        $this->title = $title;
        $this->classes = array('btn', 'btn-default');
    }
    public function show() {
        $this->displayed();
        return '<button type="button" id="'.Html::escape($this->id).'" class="'.Html::escape(implode(' ', $this->classes)).'" style="'.Html::escape($this->style).'" onclick="eventcall(\''.$this->id.'\',\'onClick\')">'.Html::content($this->title).'</button>';
    }
}
