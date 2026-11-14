<?php
use HIEQueryUI\{Button, Checkbox, Widget, Html, HIEQueryUI};

$button = new Button('Save <Zoë> 🦞');
check(!$button->disabled && $button->icon === '' && $button->ariaLabel === '', 'button defaults preserve existing behavior');
check(str_contains($button->show(), 'type="button"') && str_contains($button->show(), 'Save &lt;Zoë&gt; 🦞'), 'button remains a native escaped action');
$button->icon = '<+';
check(str_contains($button->show(), '<span aria-hidden="true">&lt;+</span> '), 'text icon escaped and decorative');
$button->icon = Html::raw('<b>+</b>');
check(str_contains($button->show(), '<span aria-hidden="true"><b>+</b></span>'), 'explicit trusted icon markup');
$button->ariaLabel = 'Save "<& 🦞';
check(str_contains($button->show(), 'aria-label="Save &quot;&lt;&amp; 🦞"'), 'accessible name escaped');
$button->disabled = true;
check(isset(Widget::$changed[$button->getId()]) && str_contains($button->show(), ' disabled'), 'disabled change queues native disabled state');
check(!isset(Widget::$changed[$button->getId()]), 'disabled render clears queue');
$checkbox = new Checkbox('Inherited'); $checkbox->disabled = true;
check(str_contains($checkbox->show(), ' disabled'), 'checkbox renders inherited button disabled state');

require_once dirname(__DIR__).'/examples/ButtonDemo.php';
$buttonApp = new HIEQueryUI(new \HIEQueryUI\Examples\ButtonDemo(), 'ajax');
$buttonApp = unserialize(serialize($buttonApp)); $demo = $buttonApp->entrypoint;
$demo->show();
check($demo->action->icon === '+' && $demo->reset->ariaLabel === 'Reset count', 'button slots survive session restoration');
[$status, $body] = request($buttonApp, $demo->action->getId(), 'onClick');
check($status === 200 && str_contains(base64_decode(json_decode($body, true)[0][$demo->status->getId()]), 'Items: 1'), 'restored button listener updates label');
[$status] = request($buttonApp, $demo->toggle->getId(), 'onClick');
check($status === 200 && $demo->action->disabled && !$demo->action->canHandle('onClick'), 'PHP disables action dispatch');
$oldValue = $demo->action->value;
[$status] = request($buttonApp, $demo->action->getId(), 'onClick', 'forged');
check($status === 400 && $demo->action->value === $oldValue && $demo->status->text === 'Items: 1', 'disabled request cannot change value or invoke listener');
check(fails(fn() => $demo->action->dispatch('onClick', base64_encode('forged'))), 'direct disabled dispatch rejected');
$buttonApp = unserialize(serialize($buttonApp)); $demo = $buttonApp->entrypoint;
check($demo->action->disabled && !$demo->action->canHandle('onClick'), 'disabled guard survives session restoration');
[$status] = request($buttonApp, $demo->toggle->getId(), 'onClick');
[$status] = request($buttonApp, $demo->action->getId(), 'onClick');
check($status === 200 && $demo->status->text === 'Items: 2', 'reenabled action resumes listener');
[$status] = request($buttonApp, $demo->reset->getId(), 'onClick');
check($status === 200 && $demo->status->text === 'Items: 0', 'icon-only action has independent listener');
Widget::$changed = array();
