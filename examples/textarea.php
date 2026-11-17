<?php
require_once dirname(__DIR__).'/HIEQueryUI.php';
require_once __DIR__.'/TextAreaDemo.php';
session_start();
if (!isset($_SESSION['hiequeryui-textarea-example'])) {
    $ui = new \HIEQueryUI\HIEQueryUI(new \HIEQueryUI\Examples\TextAreaDemo(), 'ajax');
    $ui->title = 'HIEQueryUI TextArea';
    $ui->runtimeUrl = '../assets/hiequeryui.js';
    $ui->stylesheetUrl = 'textarea.css';
    $_SESSION['hiequeryui-textarea-example'] = $ui;
}
$_SESSION['hiequeryui-textarea-example']->start();
