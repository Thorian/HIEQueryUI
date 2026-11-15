<?php
namespace HIEQueryUI\Examples;
use HIEQueryUI\{Container, Input, Label, Button, Html, Widget, InputValidator, onChange, onEnter, onClick};

class InputDemo extends Container implements InputValidator, onChange, onEnter, onClick {
    public $input;
    public $status;
    public $lock;
    public $disable;

    public function __construct() {
        parent::__construct();
        $this->add(new Label(Html::raw('<h1>Inputs</h1>')));
        $this->input = new Input('Quantity');
        $this->input->placeholder = '1 to 99';
        $this->input->name = 'quantity';
        $this->input->required = true;
        $this->input->value = '1';
        $this->input->addValidator($this);
        $this->input->addChangeListener($this);
        $this->input->addEnterListener($this);
        $this->add(new Label('Quantity', $this->input));
        $this->add($this->input);
        $this->lock = new Button('Make read-only');
        $this->disable = new Button('Disable input');
        foreach (array($this->lock, $this->disable) as $button) {
            $button->addClickListener($this);
            $this->add($button);
        }
        $this->status = new Label('Accepted quantity: 1');
        $this->status->classes = array('input-status');
        $this->add($this->status);
        $help = new Label('Enter a whole number from 1 to 99. Invalid input restores the accepted value.');
        $help->classes = array('input-help');
        $this->add($help);
    }
    public function validate(Input $input, string $value): ?string {
        return preg_match('/^[1-9][0-9]?$/D', $value) ? null : 'Enter a whole number from 1 to 99.';
    }
    public function onChange(Widget $widget) { $this->status->text = 'Accepted quantity: '.$widget->value; }
    public function onEnter(Widget $widget) { $this->onChange($widget); }
    public function onClick(Widget $widget) {
        if ($widget === $this->lock) {
            $this->input->readOnly = !$this->input->readOnly;
            $this->lock->title = $this->input->readOnly ? 'Make editable' : 'Make read-only';
        }
        if ($widget === $this->disable) {
            $this->input->disabled = !$this->input->disabled;
            $this->disable->title = $this->input->disabled ? 'Enable input' : 'Disable input';
        }
    }
}
