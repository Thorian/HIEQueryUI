<?php
namespace HIEQueryUI;
class Input extends InteractableWidget {
    protected $title;
    protected $type = 'text';
    protected $autofocus = false;
    protected bool $disabled = false;
    protected bool $readOnly = false;
    protected bool $required = false;
    protected string $placeholder = '';
    protected string $name = '';
    protected string $error = '';
    protected array $validators = array();
    public function __construct($title = '') {
        parent::__construct(); $this->title = $title; $this->classes = array('form-control');
    }
    public function __set($key, $value) {
        if ($key === 'type' && !in_array($value, array('text', 'password', 'email', 'tel', 'url', 'search', 'number', 'date', 'time', 'datetime-local', 'month', 'week', 'color', 'range'), true)) {
            throw new \InvalidArgumentException('Unsupported input type.');
        }
        parent::__set($key, $value);
    }
    public function addValidator(InputValidator $validator): void {
        $this->validators[] = $validator;
    }
    public function canHandle(string $event): bool {
        return !$this->disabled && !$this->readOnly && parent::canHandle($event);
    }
    public function dispatch(string $event, string $data): void {
        if (!$this->canHandle($event)) throw new \InvalidArgumentException('Event has no registered listener.');
        $value = base64_decode($data, true);
        if ($value === false) throw new \InvalidArgumentException('Invalid event data.');
        $error = preg_match('//u', $value) !== 1 ? 'Enter valid UTF-8 text.' : ($this->required && $value === '' ? 'Enter a value.' : null);
        if ($error === null) foreach ($this->validators as $validator) {
            $error = $validator->validate($this, $value);
            if ($error !== null) break;
        }
        if ($error !== null) {
            $this->error = $error !== '' ? $error : 'Invalid value.';
            $this->changed();
            return;
        }
        if ($this->error !== '') { $this->error = ''; $this->changed(); }
        parent::dispatch($event, $data);
    }
    public function show() {
        $this->displayed();
        return '<input id="'.Html::escape($this->id).'" type="'.Html::escape($this->type).'" value="'.Html::escape($this->value).'" aria-label="'.Html::escape($this->title).'" class="'.Html::escape(implode(' ', $this->classes)).'" name="'.Html::escape($this->name).'" placeholder="'.Html::escape($this->placeholder).'"'.(!$this->disabled && !$this->readOnly ? ' oninput="deventcall(\''.$this->id.'\',\'onChange\',event)" onkeydown="if(event.key===\'Enter\'){event.preventDefault();deventcall(\''.$this->id.'\',\'onEnter\',event)}"' : '').($this->autofocus ? ' autofocus' : '').($this->disabled ? ' disabled' : '').($this->readOnly ? ' readonly' : '').($this->required ? ' required' : '').($this->error !== '' ? ' aria-invalid="true" aria-description="'.Html::escape($this->error).'" title="'.Html::escape($this->error).'"' : '').'>';
    }
}
