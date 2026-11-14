<?php
require_once dirname(__DIR__).'/HIEQueryUI.php';
require_once __DIR__.'/ButtonDemo.php';
session_start();
if (!isset($_SESSION['hiequeryui-button-example'])) {
    $ui = new \HIEQueryUI\HIEQueryUI(new \HIEQueryUI\Examples\ButtonDemo(), 'ajax');
    $ui->title = 'HIEQueryUI Button';
    $ui->runtimeUrl = '../assets/hiequeryui.js';
    $ui->stylesheetUrl = 'demo.css';
    $_SESSION['hiequeryui-button-example'] = $ui;
}
$_SESSION['hiequeryui-button-example']->start();
