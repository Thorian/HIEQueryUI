# Persisting the UI graph

Store the wrapper in the PHP session and retain the listener objects it references. PHP serialization preserves shared references, so the controller passed to a button can remain the same object as its parent container after restoration.

Never unserialize browser-supplied data. Event payloads are Base64 strings, not serialized PHP objects. Class definitions must be loaded before PHP restores trusted server-side session data.

Widgets store presentation state. Keep database connections, resource handles, and closures out of the serialized graph; obtain application services during a request. Session storage, expiry, cookie settings, locking, authentication, and concurrent application writes remain host responsibilities.

Do not cache authenticated UI pages publicly: the wrapper emits no-store responses and its token belongs to its session. Static browser runtime/CSS assets can be served with an appropriate separate cache policy.
