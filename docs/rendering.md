# Rendering and trusted markup

Plain text is escaped in button titles, navigation labels, input values, attributes, the page title, and the shell's badge/footer. To supply deliberate markup, use Html::raw():

```php
$button = new \HIEQueryUI\Button(\HIEQueryUI\Html::raw('<b>Pay</b>'));
```

Only mark application-controlled HTML as trusted. Do not put customer names or request input into Html::raw(). Attribute values are always escaped.

show() returns one root element with the widget ID. Call displayed() when rendering a widget. changed() uses that same ID as its queue key, so repeated changes are deduplicated. Containers render their children, clearing child entries that have already been included in the parent update.

Rendering should describe state, not change it repeatedly. A render loop that keeps queueing widgets is stopped with an exception after 1,000 renders. Inline scripts in updated markup are not executed by the new runtime; bind application behavior to hiequeryui:updated instead.
