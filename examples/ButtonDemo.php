<?php
namespace HIEQueryUI\Examples;
use HIEQueryUI\{Container, Button, Label, Html, Widget, onClick};

class ButtonDemo extends Container implements onClick {
    public $action;
    public $toggle;
    public $reset;
    public $status;
    protected $count = 0;

    public function __construct() {
        parent::__construct();
        $this->add(new Label(Html::raw('<h1>Buttons</h1>')));
        $this->action = new Button('Add item');
        $this->action->icon = '+';
        $this->toggle = new Button('Disable Add item');
        $this->reset = new Button('');
        $this->reset->icon = '↺';
        $this->reset->ariaLabel = 'Reset count';
        foreach (array($this->action, $this->toggle, $this->reset) as $button) {
            $button->addClickListener($this);
            $this->add($button);
        }
        $this->status = new Label('Items: 0');
        $this->add($this->status);
    }

    public function onClick(Widget $widget) {
        if ($widget === $this->action) $this->status->text = 'Items: '.++$this->count;
        if ($widget === $this->toggle) {
            $this->action->disabled = !$this->action->disabled;
            $this->toggle->title = $this->action->disabled ? 'Enable Add item' : 'Disable Add item';
        }
        if ($widget === $this->reset) {
            $this->count = 0;
            $this->status->text = 'Items: 0';
        }
    }
}
