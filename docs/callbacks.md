# Application behavior through PHP listeners

The PHP-GTK inspiration is visible in the event model: construct interface objects, connect listeners, and handle interactions in PHP. This minimal example illustrates one connection inside an application.

```php
class Panel extends \HIEQueryUI\Container implements \HIEQueryUI\onClick {
    private $button;
    public function __construct() {
        parent::__construct();
        $this->button = new \HIEQueryUI\Button('Click me');
        $this->button->addClickListener($this);
        $this->add($this->button);
    }
    public function onClick(\HIEQueryUI\Widget $source) {
        if ($source === $this->button) $this->button->title = 'Clicked';
    }
}
```

The button knows its listeners, not the controller's business rules. The listener receives the source, changes state, and the widget queue handles rendering. A module can be removed by removing its connections and owned widgets, provided other code does not depend on its state.

Callbacks that move money or modify records still need application-level authorization, validation, idempotency, and error handling. The UI protocol does not supply those business guarantees.
