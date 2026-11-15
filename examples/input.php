<?php
require_once dirname(__DIR__).'/HIEQueryUI.php';
require_once __DIR__.'/InputDemo.php';
session_start();
if (!isset($_SESSION['hiequeryui-input-example'])) {
    $ui = new \HIEQueryUI\HIEQueryUI(new \HIEQueryUI\Examples\InputDemo(), 'ajax');
    $ui->title = 'HIEQueryUI Input';
    $ui->runtimeUrl = '../assets/hiequeryui.js';
    $ui->stylesheetUrl = 'input.css';
    $_SESSION['hiequeryui-input-example'] = $ui;
}
$_SESSION['hiequeryui-input-example']->start();
