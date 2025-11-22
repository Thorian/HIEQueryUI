<?php
require_once dirname(__DIR__).'/HIEQueryUI.php';
require_once __DIR__.'/Counter.php';
session_start();
if (!isset($_SESSION['hiequeryui-embedded'])) {
    $ui = new \HIEQueryUI\HIEQueryUI(new \HIEQueryUI\Examples\Counter(), 'embedded');
    $ui->runtimeUrl = '../assets/hiequeryui.js';
    $_SESSION['hiequeryui-embedded'] = $ui;
}
$ui = $_SESSION['hiequeryui-embedded'];
$markup = $ui->start();
if ($markup === null) return; // An event response has already been sent.
echo '<!doctype html><html><head><meta charset="utf-8"><title>Embedded widgets</title>'.$ui->assets().'</head><body><h1>Your own page shell</h1>'.$markup.'</body></html>';
