<?php
namespace HIEQueryUI;

class TextArea extends Input {
    protected int $rows = 4;
    protected int $cols = 40;
    protected ?int $minLength = null;
    protected ?int $maxLength = null;
    protected string $wrap = 'soft';

    public function __set($key, $value) {
        if ($key === 'type') throw new \InvalidArgumentException('TextArea has no input type option.');
        if (in_array($key, array('rows', 'cols'), true) && (!is_int($value) || $value < 1)) {
            throw new \InvalidArgumentException('Rows and columns must be positive integers.');
        }
        if (in_array($key, array('minLength', 'maxLength'), true)) {
            if ($value !== null && (!is_int($value) || $value < 0)) throw new \InvalidArgumentException('Length bounds must be non-negative integers or null.');
            $min = $key === 'minLength' ? $value : $this->minLength;
            $max = $key === 'maxLength' ? $value : $this->maxLength;
            if ($min !== null && $max !== null && $min > $max) throw new \InvalidArgumentException('Minimum length cannot exceed maximum length.');
        }
        if ($key === 'wrap' && !in_array($value, array('soft', 'off'), true)) throw new \InvalidArgumentException('Wrap must be soft or off.');
        parent::__set($key, $value);
    }

    public function dispatch(string $event, string $data): void {
        $value = base64_decode($data, true);
        if ($value === false) throw new \InvalidArgumentException('Invalid event data.');
        parent::dispatch($event, base64_encode(str_replace(array("\r\n", "\r"), "\n", $value)));
    }

    protected function validateValue(string $value): ?string {
        // Count UTF-16 units, matching native textarea length bounds.
        if (preg_match('//u', $value) === 1) {
            $length = preg_match_all('/./us', $value) + preg_match_all('/[\x{10000}-\x{10FFFF}]/u', $value);
            if ($this->minLength !== null && $value !== '' && $length < $this->minLength) return 'Enter at least '.$this->minLength.' characters.';
            if ($this->maxLength !== null && $length > $this->maxLength) return 'Enter no more than '.$this->maxLength.' characters.';
        }
        return parent::validateValue($value);
    }

    public function show() {
        $html = '<textarea id="'.Html::escape($this->id).'" aria-label="'.Html::escape($this->title).'" class="'.Html::escape(implode(' ', $this->classes)).'" name="'.Html::escape($this->name).'" placeholder="'.Html::escape($this->placeholder).'" rows="'.$this->rows.'" cols="'.$this->cols.'" wrap="'.$this->wrap.'"';
        if ($this->minLength !== null) $html .= ' minlength="'.$this->minLength.'"';
        if ($this->maxLength !== null) $html .= ' maxlength="'.$this->maxLength.'"';
        if ($this->autofocus) $html .= ' autofocus';
        if ($this->disabled) $html .= ' disabled';
        if ($this->readOnly) $html .= ' readonly';
        if ($this->required) $html .= ' required';
        if (!$this->disabled && !$this->readOnly) $html .= ' oninput="deventcall(\''.$this->id.'\',\'onChange\',event)" onkeydown="if(event.key===\'Enter\' &amp;&amp; (event.ctrlKey || event.metaKey)){event.preventDefault();deventcall(\''.$this->id.'\',\'onEnter\',event)}"';
        if ($this->error !== '') $html .= ' aria-invalid="true" aria-description="'.Html::escape($this->error).'" title="'.Html::escape($this->error).'"';
        $this->displayed();
        // HTML discards one initial newline immediately after the opening tag.
        return $html.">\n".Html::escape($this->value).'</textarea>';
    }
}
