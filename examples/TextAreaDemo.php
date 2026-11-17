<?php
namespace HIEQueryUI\Examples;
use HIEQueryUI\{Container, TextArea, Label, Button, Html, Widget, onChange, onEnter, onClick};

class TextAreaDemo extends Container implements onChange, onEnter, onClick {
    public $notes;
    public $status;
    public $lock;
    public $disable;

    public function __construct() {
        parent::__construct();
        $this->add(new Label(Html::raw('<h1>Notes</h1>')));
        $this->notes = new TextArea('Notes');
        $this->notes->placeholder = 'Write a few lines';
        $this->notes->maxLength = 120;
        $this->notes->value = "First line\nSecond line";
        $this->notes->addChangeListener($this);
        $this->notes->addEnterListener($this);
        $this->add(new Label('Notes', $this->notes));
        $this->add($this->notes);
        $this->lock = new Button('Make read-only');
        $this->disable = new Button('Disable notes');
        foreach (array($this->lock, $this->disable) as $button) {
            $button->addClickListener($this);
            $this->add($button);
        }
        $this->status = new Label('Enter adds a line. Ctrl+Enter or Command+Enter saves immediately.');
        $this->status->classes = array('notes-status');
        $this->add($this->status);
    }
    public function onChange(Widget $widget) { $this->status->text = 'Notes updated in PHP.'; }
    public function onEnter(Widget $widget) { $this->status->text = 'Notes saved in PHP.'; }
    public function onClick(Widget $widget) {
        if ($widget === $this->lock) {
            $this->notes->readOnly = !$this->notes->readOnly;
            $this->lock->title = $this->notes->readOnly ? 'Make editable' : 'Make read-only';
        }
        if ($widget === $this->disable) {
            $this->notes->disabled = !$this->notes->disabled;
            $this->disable->title = $this->notes->disabled ? 'Enable notes' : 'Disable notes';
        }
    }
}
