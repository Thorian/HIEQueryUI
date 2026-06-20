# Browser behavior

The browser is the framework's thin presentation layer. Application decisions and authoritative state belong to PHP.

The runtime uses fetch, URLSearchParams, TextEncoder/TextDecoder, Map, optional chaining, and native promises. Target modern browsers; no legacy-browser polyfills are bundled.

Requests are queued in order, and each captures the value when the event is requested. This prevents a later DOM replacement from changing the payload of an already-queued event. Text inputs debounce separately; Enter sends immediately for that control.

On failure, the runtime dispatches hiequeryui:error and shows a text-only banner with a Reload button. It does not automatically refresh the page or retry an application action. After changes are applied, hiequeryui:updated is dispatched for application-owned hooks.

The current widgets use inline event attributes. A strict Content Security Policy that blocks inline handlers requires a different event binding setup. Native browser interaction, accessibility, and optional AdminLTE behavior still need manual verification in target browsers.
