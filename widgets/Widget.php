<?php 
Namespace HIEQueryUI;
class Widget {
    protected $id;
    
    public static $WidgetDB = array();
    public static $changed = array();
    public $classes = array();
    
    public function __construct() {
        $this->id = uniqid(count(self::$WidgetDB));
    }
    
    public function __wakeup() {
        self::$WidgetDB[$this->id] = $this;
    }
    
    public function __set($key,$value){
        $this->changed();
        $this->$key = $value;
    }
    
    public function __get($key) {
        
        return $this->$key;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function show() {
        $this->displayed();
        return '<div id="'.$this->id.'"></div>';
    }
    
    public function displayed() {
        if(isset(self::$changed[$this->id]))
            unset(self::$changed[$this->id]);
    }
    public function changed() {
        self::$changed[] = $this;
    }
}