<?php
namespace HIEQueryUI;

class Widget {
    protected $id;
    public static $WidgetDB = array();
    public static $changed = array();
    public $classes = array();

    public function __construct() {
        $this->id = 'w'.bin2hex(random_bytes(12));
        self::$WidgetDB[$this->id] = $this;
    }
    public function __wakeup() { self::$WidgetDB[$this->id] = $this; }
    public function __set($key, $value) {
        if (!property_exists($this, $key)) throw new \OutOfBoundsException('Unknown widget property: '.$key);
        if ($this->$key !== $value) { $this->$key = $value; $this->changed(); }
    }
    public function __get($key) {
        if (!property_exists($this, $key)) throw new \OutOfBoundsException('Unknown widget property: '.$key);
        return $this->$key;
    }
    public function getId() { return $this->id; }
    public function changed() { self::$changed[$this->id] = $this; }
    public function displayed() { unset(self::$changed[$this->id]); }
    public function show() {
        $this->displayed();
        return '<div id="'.Html::escape($this->id).'"></div>';
    }
}
