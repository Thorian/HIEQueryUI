# Migrating an installed HIEQueryUI copy

Test existing applications in a separate environment before updating the library.

- Requires PHP 8.2+. Declare custom widget properties instead of creating dynamic properties through __set().
- Button/navigation/shell strings are escaped. Wrap intentionally supplied markup in Html::raw().
- GET callback URLs are replaced by POST plus the UI's token. Integrations constructing eventcall requests must follow the new protocol.
- A standalone assets/hiequeryui.js replaces duplicated inline scripts. Set runtimeUrl to a served path; set url if events should go to another same-origin route.
- New widgets register immediately, not only after session restoration.
- The changed queue is keyed by widget ID. Use changed()/displayed(), not array append operations on Widget::$changed.
- Container add/del now request a refresh automatically. Remove redundant manual scheduling where appropriate.
- onTimeout now takes Widget $widget, like the other listener interfaces.
- NavItem no longer requires gettext. Translate labels in the application.
- Inline scripts in returned HTML no longer execute automatically. Use hiequeryui:updated listeners.
- LTEAdminUI is a smaller optional shell; adapt code relying on its old stock template fields.

After deployment, restart existing UI sessions so serialized instances acquire the new request token and properties. A browser tab from an old graph must reload.
