<?php
namespace HIEQueryUI;

class Label extends Widget {
    protected string|Html $text;
    protected Input|Checkbox|null $target;

    public function __construct(string|Html $text = '', Input|Checkbox|null $target = null) {
        parent::__construct();
        $this->text = $text;
        $this->target = $target;
    }

    public function show() {
        $tag = $this->target === null ? 'span' : 'label';
        $html = '<'.$tag.' id="'.Html::escape($this->id).'" class="'.Html::escape(implode(' ', $this->classes)).'"';
        if ($this->target !== null) $html .= ' for="'.Html::escape($this->target->getId()).'"';
        $html .= '>'.Html::content($this->text).'</'.$tag.'>';
        $this->displayed();
        return $html;
    }
}
