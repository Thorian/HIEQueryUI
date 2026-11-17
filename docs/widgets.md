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

`text` and `target` use the normal widget property accessors. Assigning `null` to `target` removes the association. Invalid target types are rejected without changing the previous target. The target is a reference, not an owned child: add both widgets to the interface, and keep their lifetimes together. Input (including TextArea) and Checkbox are supported targets. An existing control's `aria-label` still supplies its accessible name; use matching text when pairing it with a visible Label.

Labels are passive: they register no events and need no extra JavaScript or CSS. Native labels focus inputs and toggle checkboxes. They do not have disabled/read-only state or add a tab stop. Text, target references, IDs and classes survive session restoration. Changing public `classes` requires `changed()`, as on other widgets. See the [standalone example](../examples/label.php) for several labels and a PHP listener that updates one after typing.

## Button

`new Button($title = '')` creates a native action button. `title` remains its text slot and accepts escaped text or explicit `Html::raw()` markup. The button has `type="button"`, so it does not submit an enclosing form. Enter and Space use the browser's normal button behavior.

```php
$save = new \HIEQueryUI\Button('Save');
$save->icon = '✓';
$save->addClickListener($controller);
$save->disabled = true;
```

`disabled` defaults to false. When true, the browser disables the control and PHP rejects its events before changing its value or calling a listener. Set it back to false to enable the action. The state survives session restoration. Checkbox inherits this guard and renders the same disabled attribute.

`icon` defaults to an empty string and accepts text or `Html::raw()` markup. It appears before the title in a decorative `aria-hidden` span. The framework supplies no icon font; applications can use a character or their own trusted markup. Keep interactive elements out of both content slots.

`ariaLabel` supplies an optional escaped accessible name. For an icon-only button, use an empty title and set `ariaLabel` to describe its action. For a text button, the visible title supplies the name by default. Changing these protected properties queues the normal refresh. Existing `classes` and `style` remain available.

The [Button example](../examples/button.php) includes an add action, a separate enable/disable action, and an icon-only reset action. The PHP controller owns the count and enabled state; no extra JavaScript is needed.

## Input

`new Input($title = '')` creates a single-value input whose title supplies its accessible name. `value` is the accepted value. Options include `placeholder` and `name` (empty strings by default), `autofocus`, `required`, `readOnly`, and `disabled` (false by default).

`type` defaults to `text`. Supported values are text, password, email, tel, url, search, number, date, time, datetime-local, month, week, color, and range. Other types are rejected when assigned. The type controls native browser presentation; it does not add PHP email, URL, date, or numeric validation. Use a validator for application rules.

```php
class QuantityValidator implements \HIEQueryUI\InputValidator {
    public function validate(\HIEQueryUI\Input $input, string $value): ?string {
        return preg_match('/^[1-9][0-9]?$/D', $value)
            ? null : 'Enter a whole number from 1 to 99.';
    }
}
$input = new \HIEQueryUI\Input('Quantity');
$input->required = true;
$input->placeholder = '1 to 99';
$input->addValidator(new QuantityValidator());
```

Validators run in registration order before the value changes or a listener runs. Return null to accept, or an error string to reject. The first rejection stops validation. UTF-8 and required-value checks run first; required rejects an empty string, while whitespace rules belong in a validator. Validators should inspect the proposed value without changing the input. They are serialized with the widget, so use named classes and load them before starting the session.

On rejection, PHP keeps the previous value, skips the listener, and refreshes the input with `aria-invalid`, an escaped `aria-description`, and a hover title. The error is available as `error`; a later accepted event clears it. The browser displays the previous accepted value again. Applications can provide additional visible guidance alongside the field. Invalid input is a normal widget update, not an HTTP failure.

Disabled and read-only inputs reject browser events in PHP, including crafted requests. Read-only text inputs remain focusable in the browser; disabled inputs are skipped by Tab. Native read-only behavior varies by input type, but the PHP guard applies to every type. Both states omit the event bindings. Programmatic value assignments remain available and do not run validators. Changing options queues the usual widget update. The [standalone example](../examples/input.php) shows validation, Enter, and toggling both states from PHP.

## TextArea

`new TextArea($title = '')` extends Input for multiline text. It inherits `value`, `placeholder`, `name`, `autofocus`, `required`, `readOnly`, `disabled`, `error`, and `addValidator()`. Existing Label widgets can target a TextArea. It has no `type` option.

```php
$notes = new \HIEQueryUI\TextArea('Delivery notes');
$notes->rows = 6;
$notes->maxLength = 500;
$notes->value = "First line\nSecond line";
$notes->addChangeListener($controller);
$notes->addEnterListener($controller);
```

`rows` defaults to 4 and `cols` to 40; both require positive integers. `wrap` is `soft` by default, or `off` to scroll long lines horizontally. `minLength` and `maxLength` default to null (no bound). Bounds accept non-negative integers and cannot cross. Like native textarea limits, lengths count UTF-16 units: most characters count once, while characters such as 🦞 count twice. An optional empty value is allowed even with a minimum; set `required` to reject empty text.

Typing sends the usual debounced `onChange` event. Enter inserts a newline. Ctrl+Enter or Command+Enter flushes pending typing and invokes `onEnter` immediately. Browser line endings are normalized to LF before length checks and Input validators run. Length errors keep the previous accepted value, skip listeners, and use the same accessible error attributes as Input. Programmatic assignments do not run validation.

Text is always escaped, including closing textarea tags; `Html::raw()` is not supported as a value. An initial newline is preserved when the browser parses the element. Text, bounds, validators, and lock states survive session restoration. Read-only controls remain focusable; disabled controls are skipped by Tab. Both reject crafted events in PHP. See the [notes example](../examples/textarea.php).
