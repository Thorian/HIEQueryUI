# Browser events

The supported events are onClick, onChange, onEnter, and onTimeout. A target must be an InteractableWidget with a listener registered for that event. Public methods are not automatically browser-callable.

Requests use POST with source, event, data, and csrf. data is a UTF-8 string encoded as Base64; the browser uses URLSearchParams to preserve plus signs and punctuation. The token belongs to the session-persisted wrapper. It is request protection, not application authorization.

AJAX requests also include aj. The response remains an array containing a map from widget IDs to Base64-encoded HTML. Errors contain an error object and an appropriate HTTP status: 400 for invalid fields/events, 403 for stale tokens, 405 for GET events, and 410 for an unavailable control.

Input change callbacks are debounced per control. Enter flushes that control's pending debounce. The current implementation does not schedule browser timeout events automatically; onTimeout is available for an application-controlled scheduler.
