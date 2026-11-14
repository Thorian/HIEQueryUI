<?php
namespace HIEQueryUI\Examples;
use HIEQueryUI\{Container, Label, Input, Checkbox, Html, Widget, onChange, onClick};

class LabelDemo extends Container implements onChange, onClick {
    public $input;
    public $status;
    public $checkbox;

    public function __construct() {
        parent::__construct();
        $this->add(new Label(Html::raw('<h1>Labels</h1>')));
        $this->input = new Input('Your name');
        $this->input->addChangeListener($this);
        $this->add(new Label('Your name', $this->input));
        $this->add($this->input);
        $option = $this->checkbox = new Checkbox('Receive updates');
        $option->addClickListener($this);
        $this->add(new Label('Receive updates', $option));
        $this->add($option);
        $this->status = new Label('Enter a name to update this text through PHP.');
        $this->add($this->status);
    }

    public function onChange(Widget $widget) {
        $this->status->text = $widget->value === '' ? 'Enter a name to update this text through PHP.' : 'Hello, '.$widget->value;
    }

    public function onClick(Widget $widget) {
        $this->status->text = $widget->value ? 'Updates enabled.' : 'Updates disabled.';
    }
}
