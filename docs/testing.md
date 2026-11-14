# Running tests

From the repository root:

```sh
php tests/run.php
node tests/runtime.js
node tests/http.js
```

The first test runs PHP widget and request checks without a database. The browser-runtime test executes the real JavaScript in a small mock DOM to check transport, Unicode, debounce, and request ordering. It is not a replacement for real browser testing.

The HTTP test starts an isolated PHP development server on loopback, uses test sessions, checks full/AJAX/embedded modes and rejects invalid requests, then stops its own child process. Pass the PHP executable path as an argument if it is not on PATH. Set HIE_TEST_PORT to choose another port.

The GitHub workflow tests PHP 8.2, 8.3, and 8.4 with Node 22. Local checks used PHP 8.4.26 and Node. Check the Actions results for the other PHP versions.

Label was checked locally with PHP 8.4.26 and the in-app browser: caption clicks focus the input or toggle the checkbox, Tab/Space operate the controls, Unicode text updates through PHP, and values and associations survive reload. The PHP suite also covers invalid targets, explicit trusted markup, passive-event rejection, and parent refreshes. Other browser engines have not been checked for this widget.

Button was checked with PHP 8.4.26 and the in-app browser: Enter and Space activation, icon-only accessible naming, disabled controls skipped by Tab, focus after updates, and disabled state after reload. PHP and HTTP tests also check that crafted events cannot invoke a disabled action. Other browser engines have not been checked for this update.
