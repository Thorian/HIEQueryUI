<?php
require dirname(__DIR__).'/HIEQueryUI.php';
$widget = new \HIEQueryUI\Widget();
if (!str_contains($widget->show(), $widget->getId())) throw new RuntimeException('Widget root is missing its ID.');
echo "PASS bootstrap\n";
