# Contributing

Bug reports, examples, documentation fixes, and new widgets are welcome.

For a bug report, include a small example that reproduces the problem, what you expected to happen, and your PHP and browser versions. Please report security issues privately as described in [SECURITY.md](SECURITY.md).

For a new feature or a breaking change, open an issue first so we can discuss the API. The [roadmap](ROADMAP.md) lists planned widgets and improvements.

HIEQueryUI follows the PHP-GTK programming model: application objects and listeners live in PHP, while the browser handles input and displays the result. Contributions should fit that design and work with session serialization.

Keep each contribution focused. A widget contribution should include a standalone example, tests, and API documentation. The [widget guide](docs/widget-development.md) explains the rendering and event conventions.

Run the [test suite](docs/testing.md) before submitting a pull request. Include a regression test for a bug fix, and mention any manual browser checks in the pull request description.
