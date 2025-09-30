<?php
namespace HIEQueryUI\Examples;
use HIEQueryUI\{Container, Button, Input, Checkbox, Widget, onClick, onChange, onEnter};

class Counter extends Container implements onClick, onChange, onEnter {
    public $button;
    public $input;
    public $checkbox;
    protected $count = 0;
    public function __construct() {
        parent::__construct();
        $this->button = new Button('Clicks: 0'); $this->button->addClickListener($this); $this->add($this->button);
        $this->input = new Input('Type a name'); $this->input->addChangeListener($this); $this->input->addEnterListener($this); $this->add($this->input);
        $this->checkbox = new Checkbox('Enable an option'); $this->checkbox->addClickListener($this); $this->add($this->checkbox);
    }
    public function onClick(Widget $widget) {
        if ($widget === $this->button) $this->button->title = 'Clicks: '.++$this->count;
    }
    public function onChange(Widget $widget) { $this->input->title = 'Hello '.($widget->value ?: 'there'); }
    public function onEnter(Widget $widget) { $this->onChange($widget); }
}
