# Themes and browser assets

The core runtime uses browser APIs and has no jQuery requirement. runtimeUrl identifies assets/hiequeryui.js; stylesheetUrl optionally points to application CSS. Both must be served by the host application.

LTEAdminUI is an optional AdminLTE 3 shell. To use it, point the wrapper's filebase at an AdminLTE 3 asset directory containing plugins/jquery, plugins/bootstrap, plugins/fontawesome-free, and dist. Those packages are not bundled here. Supply and review their versions and licenses in your application.

Set the shell's badge and footer from your application; strings are escaped, and Html::raw() enables trusted markup. The shell uses the same Navbar and Viewport as an application-owned layout, keeping presentation separate from application behavior.

The demo CSS is intentionally modest and can be replaced. No theme compiler or frontend bundler is needed by the library.
