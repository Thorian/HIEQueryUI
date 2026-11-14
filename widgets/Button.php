<?php
namespace HIEQueryUI;
class Button extends InteractableWidget {
    protected $title;
    protected $style = '';
    protected bool $disabled = false;
    protected string|Html $icon = '';
    protected string $ariaLabel = '';
    public function __construct($title = '') {
        parent::__construct();
        $this->title = $title;
        $this->classes = array('btn', 'btn-default');
    }
    public function canHandle(string $event): bool {
        return !$this->disabled && parent::canHandle($event);
    }
    public function show() {
        $this->displayed();
        $icon = $this->icon === '' ? '' : '<span aria-hidden="true">'.Html::content($this->icon).'</span> ';
        return '<button type="button" id="'.Html::escape($this->id).'" class="'.Html::escape(implode(' ', $this->classes)).'" style="'.Html::escape($this->style).'"'.($this->disabled ? ' disabled' : '').($this->ariaLabel !== '' ? ' aria-label="'.Html::escape($this->ariaLabel).'"' : '').' onclick="eventcall(\''.$this->id.'\',\'onClick\')">'.$icon.Html::content($this->title).'</button>';
    }
}
