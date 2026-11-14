# Widget API

Widgets are the interface components of HIEQueryUI's PHP-GTK-inspired application model. Applications combine them with persistent PHP state, listeners, and services. This page describes the available components.

Widget supplies ID registration, protected-property access, changed(), displayed(), and show(). classes remains a public list of CSS classes; call changed() when modifying public properties directly.

InteractableWidget adds value and typed listener registration. Button, Input, and Checkbox are browser controls. Listener interfaces receive the source Widget; applications choose how to interpret value.

Container holds children through add()/del(). Direct and indirect cycles through containers are rejected. Viewport displays a single target via set(). Navbar connects NavItems to a Viewport; add() returns its NavItem. LTEAdminUI combines navigation and a viewport in an optional theme shell.

Html::escape() encodes text and attributes. Html::raw() is the explicit trusted-markup wrapper accepted by text-content slots.

The [roadmap](../ROADMAP.md) lists planned components. See [Writing a widget](widget-development.md) for rendering, event, and session conventions.

Alert, Card, Box, Chat, Social, ProgressBar and the other planned additions are not implemented yet.

## Label

`new Label(string|Html $text = '', Input|Checkbox|null $target = null)` displays escaped text, or explicitly trusted markup from `Html::raw()`. With no target it renders a `span`; with a target it renders a native `label` whose `for` points to that control's ID.

```php
$name = new \HIEQueryUI\Input('Your name');
$caption = new \HIEQueryUI\Label('Your name', $name);
$container->add($caption);
$container->add($name);
$caption->text = 'Customer name'; // Queues a server-rendered update.
```

`text` and `target` use the normal widget property accessors. Assigning `null` to `target` removes the association. Invalid target types are rejected without changing the previous target. The target is a reference, not an owned child: add both widgets to the interface, and keep their lifetimes together. Only Input and Checkbox are supported targets for now. An existing control's `aria-label` still supplies its accessible name; use matching text when pairing it with a visible Label.

Labels are passive: they register no events and need no extra JavaScript or CSS. Native labels focus inputs and toggle checkboxes. They do not have disabled/read-only state or add a tab stop. Text, target references, IDs and classes survive session restoration. Changing public `classes` requires `changed()`, as on other widgets. See the [standalone example](../examples/label.php) for several labels and a PHP listener that updates one after typing.
