<?php
namespace HIEQueryUI;

class InteractableWidget extends Widget {
    protected $listeners = array();
    protected $value = '';
    public function addClickListener(onClick $c) { $this->listeners['onClick'][] = $c; }
    public function addChangeListener(onChange $c) { $this->listeners['onChange'][] = $c; }
    public function addEnterListener(onEnter $c) { $this->listeners['onEnter'][] = $c; }
    public function addTimeoutListener(onTimeout $c) { $this->listeners['onTimeout'][] = $c; }
    public function canHandle(string $event): bool {
        return in_array($event, array('onClick', 'onChange', 'onEnter', 'onTimeout'), true)
            && !empty($this->listeners[$event]);
    }
    public function dispatch(string $event, string $data): void {
        if (!$this->canHandle($event)) throw new \InvalidArgumentException('Event has no registered listener.');
        $value = base64_decode($data, true);
        if ($value === false) throw new \InvalidArgumentException('Invalid event data.');
        $this->value = $value;
        foreach ($this->listeners[$event] as $listener) $listener->$event($this);
    }
    public function onClick($data) { $this->dispatch('onClick', $data); }
    public function onChange($data) { $this->dispatch('onChange', $data); }
    public function onEnter($data) { $this->dispatch('onEnter', $data); }
    public function onTimeout($data) { $this->dispatch('onTimeout', $data); }
}
