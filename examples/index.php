<?php
// Class definitions must be loaded before session_start() restores the object graph.
require_once dirname(__DIR__).'/HIEQueryUI.php';
require_once __DIR__.'/Counter.php';
session_start();
if (!isset($_SESSION['hiequeryui-example'])) {
    $ui = new \HIEQueryUI\HIEQueryUI(new \HIEQueryUI\Examples\Counter(), 'ajax');
    $ui->title = 'HIEQueryUI Counter';
    $ui->runtimeUrl = '../assets/hiequeryui.js';
    $ui->stylesheetUrl = 'demo.css';
    $_SESSION['hiequeryui-example'] = $ui;
}
$_SESSION['hiequeryui-example']->start();
