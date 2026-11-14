<?php
require_once dirname(__DIR__).'/HIEQueryUI.php';
require_once __DIR__.'/LabelDemo.php';
session_start();
if (!isset($_SESSION['hiequeryui-label-example'])) {
    $ui = new \HIEQueryUI\HIEQueryUI(new \HIEQueryUI\Examples\LabelDemo(), 'ajax');
    $ui->title = 'HIEQueryUI Label';
    $ui->runtimeUrl = '../assets/hiequeryui.js';
    $ui->stylesheetUrl = 'demo.css';
    $_SESSION['hiequeryui-label-example'] = $ui;
}
$_SESSION['hiequeryui-label-example']->start();
