<?php
require dirname(__DIR__).'/HIEQueryUI.php';
require dirname(__DIR__).'/examples/Counter.php';
use HIEQueryUI\{Widget, Button, Input, Checkbox, Html, Container, Viewport, Navbar, LTEAdminUI, HIEQueryUI};
set_error_handler(function($code, $message, $file, $line) { throw new ErrorException($message, 0, $code, $file, $line); });
$checks = 0;
function check($condition, $message) {
    global $checks; $checks++;
    if (!$condition) throw new RuntimeException($message);
}
function fails($operation) {
    try { $operation(); } catch (Throwable $e) { return true; }
    return false;
}
function config($app) {
    preg_match('~id="hiequeryui-config">(.*?)</script>~s', $app->assets(), $match);
    return json_decode($match[1], true, 512, JSON_THROW_ON_ERROR);
}
function request($app, $source, $event, $value = '', $override = array(), $method = 'POST') {
    $_SERVER['REQUEST_METHOD'] = $method;
    $_POST = array_merge(array('source' => $source, 'event' => $event, 'data' => base64_encode($value), 'aj' => 'an', 'csrf' => config($app)['csrf']), $override);
    $_REQUEST = $_POST;
    http_response_code(200);
    ob_start(); $app->start(); $text = ob_get_clean();
    return array(http_response_code(), $text);
}

$button = new Button('A < B');
check(Widget::$WidgetDB[$button->getId()] === $button, 'fresh widget registered');
check(str_contains($button->show(), 'A &lt; B'), 'button text escaped');
$button->title = Html::raw('<b>Trusted</b>');
check(str_contains($button->show(), '<b>Trusted</b>'), 'explicit trusted markup');
Widget::$changed = array(); $button->changed(); $button->changed();
check(count(Widget::$changed) === 1, 'queue deduplicated');
$button->show(); check(!Widget::$changed, 'render clears queue');
$container = new Container(); $container->add($button);
check(isset(Widget::$changed[$container->getId()]), 'adding child changes parent');
$container->show(); $container->del($button);
check(isset(Widget::$changed[$container->getId()]), 'removing child changes parent');
$other = new Container(); $container->add($other);
check(fails(fn() => $other->add($container)), 'indirect cycle rejected');
check(fails(fn() => $container->add($container)), 'self cycle rejected');
$input = new Input('Your name'); $input->value = '"><script>x</script>';
check(str_contains($input->show(), '&quot;&gt;&lt;script&gt;'), 'input attribute escaped');
check(str_contains($input->show(), 'oninput=') && str_contains($input->show(), 'onkeydown='), 'typing and Enter bound separately');
check(fails(function() use ($input) { $input->unknown = 1; }), 'unknown properties rejected');
$checkbox = new Checkbox('Test'); $checkbox->value = '1';
check(str_contains($checkbox->show(), ' checked'), 'checkbox checked');
$viewport = new Viewport(); $viewport->set(new Button('Inside'));
check(str_contains($viewport->show(), 'Inside'), 'viewport renders selected child');
$nav = new Navbar(); $nav->viewport = $viewport; $item = $nav->add('<Page>', $button, 'fas fa-home');
$nav->onClick($item);
check($viewport->target === $button && str_contains($nav->show(), '&lt;Page&gt;'), 'navigation without gettext');
$shell = new LTEAdminUI(); $shell->footer = 'Harbour Island Ecologics Inc.';
check(str_contains($shell->show(), 'Harbour Island Ecologics Inc.') && !str_contains($shell->show(), 'Alexander Pierce'), 'shell contains no fake user');

$root = new \HIEQueryUI\Examples\Counter(); $app = new HIEQueryUI($root, 'ajax');
$app->url = '/subdir/examples/index.php';
check(config($app)['endpoint'] === '/subdir/examples/index.php', 'endpoint configurable');
$app->url = '//example.invalid'; check(fails(fn() => $app->assets()), 'external endpoint rejected');
$app->url = '/subdir/examples/index.php';
$app = unserialize(serialize($app)); $root = $app->entrypoint;
check(Widget::$WidgetDB[$root->button->getId()] === $root->button, 'restored widget registered');
$root->show();
[$status, $body] = request($app, $root->button->getId(), 'onClick');
check($status === 200 && str_contains(base64_decode(json_decode($body, true)[0][$root->button->getId()]), 'Clicks: 1'), 'callback updates button');
[$status] = request($app, $root->button->getId(), 'show'); check($status === 400, 'public non-event method rejected');
[$status] = request($app, $root->button->getId(), 'onClick', '', array('csrf' => 'bad')); check($status === 403, 'csrf checked');
[$status] = request($app, 'missing', 'onClick'); check($status === 410, 'expired widget handled');
[$status] = request($app, $root->button->getId(), 'onClick', '', array('data' => '%%%')); check($status === 400, 'invalid base64 handled');
[$status] = request($app, $root->button->getId(), 'onClick', '', array(), 'GET'); check($status === 405, 'GET callbacks rejected');
[$status] = request($app, $root->button->getId(), 'onClick', '', array('source' => array('bad'))); check($status === 400, 'array request fields rejected');
[$status] = request($app, $root->input->getId(), 'onChange', 'Zoë + 🦞 & <test>');
check($status === 200 && $root->input->title === 'Hello Zoë + 🦞 & <test>', 'unicode input round trip');
[$status] = request($app, $root->input->getId(), 'onEnter', 'Enter'); check($root->input->title === 'Hello Enter', 'Enter callback');
[$status] = request($app, $root->checkbox->getId(), 'onClick', '1'); check($root->checkbox->value === '1', 'checkbox callback');
check(HIEQueryUI::$me === false, 'request state cleared');
$_POST = $_REQUEST = array(); $_SERVER['REQUEST_METHOD'] = 'GET';
$app->type = 'full'; ob_start(); $app->start(); $html = ob_get_clean();
check(str_contains($html, 'hiequeryui.js') && str_contains($html, '"mode":"full"'), 'full mode has input runtime');
$app->type = 'embedded'; check(str_contains($app->start(), 'Clicks: 1'), 'embedded mode returns markup');
$app->type = 'ajax'; $root->show();
$root->button->changed(); $root->changed(); $updates = $app->updates();
check(!Widget::$changed && str_contains(base64_decode($updates[$root->getId()]), 'Clicks: 1'), 'nested updates drain queue');
require __DIR__.'/label.php';
require __DIR__.'/button.php';
require __DIR__.'/input.php';
echo 'PASS '.$checks." PHP checks\n";
