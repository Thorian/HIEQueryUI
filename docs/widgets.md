# Widget API

Widgets are the interface components of HIEQueryUI's PHP-GTK-inspired application model. Applications combine them with persistent PHP state, listeners, and services. This page describes the available components.

Widget supplies ID registration, protected-property access, changed(), displayed(), and show(). classes remains a public list of CSS classes; call changed() when modifying public properties directly.

InteractableWidget adds value and typed listener registration. Button, Input, and Checkbox are browser controls. Listener interfaces receive the source Widget; applications choose how to interpret value.

Container holds children through add()/del(). Direct and indirect cycles through containers are rejected. Viewport displays a single target via set(). Navbar connects NavItems to a Viewport; add() returns its NavItem. LTEAdminUI combines navigation and a viewport in an optional theme shell.

Html::escape() encodes text and attributes. Html::raw() is the explicit trusted-markup wrapper accepted by text-content slots.

The [roadmap](../ROADMAP.md) lists planned components. See [Writing a widget](widget-development.md) for rendering, event, and session conventions.

Alert, Card, Box, Chat, Social, ProgressBar and the other planned additions are not implemented yet.
