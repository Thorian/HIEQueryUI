# Writing a widget

Widgets extend Widget, or InteractableWidget when they accept browser events. Existing components in [widgets/](../widgets/) provide examples; [Button](../widgets/Button.php) is a simple starting point.

## State and rendering

Declare widget properties and their defaults. Protected properties use the base class's accessors, which queue a refresh when a value changes. Public properties such as classes need an explicit changed() call after modification.

show() returns one root element with the widget's ID and calls displayed() to clear its queued update. Escape text and attributes with Html::escape(). Content slots that accept trusted markup can use Html::content(), allowing the application to supply Html::raw() explicitly.

Document accepted values, defaults, bounds, and disabled or read-only behavior where relevant. A widget that refers to another widget should explain whether it owns that child or only holds a reference.

## Events

Use the existing listener interfaces where they fit. Browser requests can invoke only registered events on an InteractableWidget; ordinary public methods are not event handlers.

PHP validates incoming values before accepting a change, including option membership, numeric bounds, and permissions supplied by the application. Rejected input should leave the last accepted state intact.

The browser handles keyboard input, focus, pointer movement, and temporary visual feedback. For a drag or resize, send the final result to PHP rather than making a request for every movement. A form submission needs a complete snapshot of its fields, since earlier typing events may still be waiting in the debounce queue.

## Sessions

Widgets and listeners are serialized as part of the application graph. Store state and widget references in that graph; obtain database connections and other services during the request. Closures and resource handles cannot be stored in the session graph.

## Examples and tests

Include a standalone example that runs without a database or another application. Cover default and changed values, escaping, Unicode, invalid input, registered events, and session restoration. Check updates to both the widget and its parent.

In a browser, check multiple instances, keyboard operation, accessible labels, focus after updates, and disabled/read-only behavior where supported. See [Running tests](testing.md) for the automated checks.
