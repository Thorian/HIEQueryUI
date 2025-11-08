<?php
require_once dirname(__DIR__).'/HIEQueryUI.php';
require_once __DIR__.'/Counter.php';
session_start();
if (!isset($_SESSION['hiequeryui-full'])) {
    $ui = new \HIEQueryUI\HIEQueryUI(new \HIEQueryUI\Examples\Counter(), 'full');
    $ui->runtimeUrl = '../assets/hiequeryui.js';
    $_SESSION['hiequeryui-full'] = $ui;
}
$_SESSION['hiequeryui-full']->start();
