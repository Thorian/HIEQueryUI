<?php
namespace HIEQueryUI;
class Container extends Widget {
    protected $childs = array();
    public function add(Widget $child) {
        if ($child === $this || ($child instanceof self && $child->contains($this))) {
            throw new \InvalidArgumentException('Containers cannot contain a cycle.');
        }
        $this->childs[] = $child;
        $this->changed();
    }
    public function del(Widget $child) {
        foreach ($this->childs as $key => $item) {
            if ($item === $child) { unset($this->childs[$key]); $this->changed(); }
        }
    }
    private function contains(Widget $target): bool {
        foreach ($this->childs as $child) {
            if ($child === $target || ($child instanceof self && $child->contains($target))) return true;
        }
        return false;
    }
    public function show() {
        $this->displayed();
        $html = '<div id="'.Html::escape($this->id).'" class="'.Html::escape(implode(' ', $this->classes)).'">';
        foreach ($this->childs as $child) $html .= $child->show();
        return $html.'</div>';
    }
}
