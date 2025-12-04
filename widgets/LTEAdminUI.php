<?php
namespace HIEQueryUI;

// Optional AdminLTE 3 shell. Browser assets are supplied through HIEQueryUI.filebase.
class LTEAdminUI extends Widget {
    public $title = 'HIEQueryUI';
    public $badge = 'HIEQueryUI';
    public $footer = '';
    public $sidemenu;
    public $viewport;
    public function __construct() {
        parent::__construct();
        $this->viewport = new Viewport();
        $this->sidemenu = new Navbar();
        $this->sidemenu->viewport = $this->viewport;
    }
    public function show() {
        $this->displayed();
        return '<body id="'.Html::escape($this->id).'" class="hold-transition sidebar-mini"><div class="wrapper">'
            .'<nav class="main-header navbar navbar-expand navbar-white navbar-light"><a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Toggle navigation"><span aria-hidden="true">☰</span></a></nav>'
            .'<aside class="main-sidebar sidebar-dark-primary elevation-4"><div class="brand-link">'.Html::content($this->badge).'</div><div class="sidebar"><nav>'.$this->sidemenu->show().'</nav></div></aside>'
            .'<main class="content-wrapper"><div class="content">'.$this->viewport->show().'</div></main>'
            .'<footer class="main-footer">'.Html::content($this->footer).'</footer></div></body>';
    }
}
