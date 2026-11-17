<?php
use HIEQueryUI\{TextArea, Input, InputValidator, Label, Widget, HIEQueryUI};

class NotesValidator implements InputValidator {
    public function validate(Input $input, string $value): ?string {
        return str_contains($value, 'blocked') ? 'No <blocked> "text".' : null;
    }
}
$notes = new TextArea('Notes');
check($notes->rows === 4 && $notes->cols === 40 && $notes->wrap === 'soft', 'textarea defaults');
$notes->value = "\nZoë 🦞\n</textarea><script>bad</script>";
$html = $notes->show();
check(str_contains($html, ">\n\nZoë 🦞\n&lt;/textarea&gt;&lt;script&gt;"), 'textarea preserves leading newline and escapes closing tags');
check(substr_count($html, '</textarea>') === 1, 'textarea has one escaped content root');
check(str_contains((new Label('Notes', $notes))->show(), 'for="'.$notes->getId().'"'), 'existing label supports textarea');
check(fails(function() use ($notes) { $notes->rows = 0; }) && $notes->rows === 4, 'invalid rows rejected');
check(fails(function() use ($notes) { $notes->cols = '20'; }), 'columns require integer');
check(fails(function() use ($notes) { $notes->wrap = 'invalid'; }), 'invalid wrap rejected');
check(fails(function() use ($notes) { $notes->type = 'email'; }), 'textarea rejects input type setting');
$notes->minLength = 2; $notes->maxLength = 5;
check(fails(function() use ($notes) { $notes->minLength = 6; }) && $notes->minLength === 2, 'crossed bounds leave previous minimum intact');
check(fails(function() use ($notes) { $notes->maxLength = -1; }) && $notes->maxLength === 5, 'negative bound rejected');
check(str_contains($notes->show(), 'minlength="2" maxlength="5"'), 'native bounds rendered');

require_once dirname(__DIR__).'/examples/TextAreaDemo.php';
$notesApp = new HIEQueryUI(new \HIEQueryUI\Examples\TextAreaDemo(), 'ajax');
$notesApp = unserialize(serialize($notesApp)); $demo = $notesApp->entrypoint; $demo->show();
[$status] = request($notesApp, $demo->notes->getId(), 'onChange', "Zoë 🦞\r\nSecond\rThird");
check($status === 200 && $demo->notes->value === "Zoë 🦞\nSecond\nThird", 'textarea restores listeners and normalizes browser line endings');
$demo->notes->maxLength = 3; $demo->notes->value = 'OK'; $demo->notes->show();
[$status] = request($notesApp, $demo->notes->getId(), 'onChange', '🦞a');
check($status === 200 && $demo->notes->value === '🦞a', 'astral character counts as two UTF-16 units');
[$status, $body] = request($notesApp, $demo->notes->getId(), 'onChange', '🦞ab');
$html = base64_decode(json_decode($body, true)[0][$demo->notes->getId()]);
check($status === 200 && $demo->notes->value === '🦞a' && str_contains($html, 'aria-invalid="true"'), 'crafted overlength request preserves previous value');
$demo->notes->minLength = 2;
request($notesApp, $demo->notes->getId(), 'onChange', 'a');
check($demo->notes->value === '🦞a' && str_contains($demo->notes->error, 'at least 2'), 'minimum bound checked in PHP');
request($notesApp, $demo->notes->getId(), 'onChange', '');
check($demo->notes->value === '' && $demo->notes->error === '', 'optional empty value allowed with minimum length');
$demo->notes->required = true;
request($notesApp, $demo->notes->getId(), 'onEnter', '');
check($demo->notes->error === 'Enter a value.', 'textarea inherits required validation');
$demo->notes->minLength = null; $demo->notes->maxLength = null;
$demo->notes->addValidator(new NotesValidator());
$notesApp = unserialize(serialize($notesApp)); $demo = $notesApp->entrypoint; $demo->show();
[$status, $body] = request($notesApp, $demo->notes->getId(), 'onChange', 'blocked');
check(str_contains(base64_decode(json_decode($body, true)[0][$demo->notes->getId()]), 'No &lt;blocked&gt; &quot;text&quot;.'), 'restored application validator error escaped');
request($notesApp, $demo->notes->getId(), 'onEnter', "Saved\nnotes");
check($demo->status->text === 'Notes saved in PHP.' && $demo->notes->error === '', 'textarea Enter listener accepts multiline value');
request($notesApp, $demo->lock->getId(), 'onClick');
check(str_contains($demo->notes->show(), ' readonly') && !str_contains($demo->notes->show(), 'oninput='), 'readonly textarea omits handlers');
[$status] = request($notesApp, $demo->notes->getId(), 'onChange', 'forged');
check($status === 400 && $demo->notes->value === "Saved\nnotes", 'readonly textarea rejects crafted change');
request($notesApp, $demo->lock->getId(), 'onClick');
request($notesApp, $demo->disable->getId(), 'onClick');
$notesApp = unserialize(serialize($notesApp)); $demo = $notesApp->entrypoint;
[$status] = request($notesApp, $demo->notes->getId(), 'onEnter', 'forged');
check($status === 400 && $demo->notes->disabled && str_contains($demo->notes->show(), ' disabled'), 'restored disabled textarea rejects crafted Enter');
request($notesApp, $demo->disable->getId(), 'onClick');
request($notesApp, $demo->notes->getId(), 'onChange', "Ready\nagain");
check($demo->notes->value === "Ready\nagain", 'reenabled textarea accepts changes');
$demo->notes->changed(); $demo->changed(); $updates = $notesApp->updates();
check(!Widget::$changed && str_contains(base64_decode($updates[$demo->getId()]), "Ready\nagain"), 'textarea parent refresh drains changes');
