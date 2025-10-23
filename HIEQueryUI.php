<?php
namespace HIEQueryUI;

spl_autoload_register(function($class) {
    $prefix = __NAMESPACE__.'\\';
    if (!str_starts_with($class, $prefix)) return;
    $name = substr($class, strlen($prefix));
    if (!preg_match('/^[A-Za-z][A-Za-z0-9]*$/D', $name)) return;
    foreach (array('widgets', 'interfaces') as $directory) {
        $file = __DIR__.'/'.$directory.'/'.$name.'.php';
        if (is_file($file)) { require_once $file; return; }
    }
});

class HIEQueryUI {
    public static $me = false;
    public $url = null;
    public $type;
    public $filebase = '';
    public $runtimeUrl = 'assets/hiequeryui.js';
    public $stylesheetUrl = '';
    public $title = 'HIEQueryUI';
    public $entrypoint;
    protected $csrfToken;

    public function __construct(Widget $entrypoint, $type = 'full') {
        if (!in_array($type, array('full', 'ajax', 'embedded'), true)) throw new \InvalidArgumentException('Unknown rendering mode.');
        $this->entrypoint = $entrypoint;
        $this->type = $type;
        $this->csrfToken = bin2hex(random_bytes(32));
    }

    public function assets(): string {
        $endpoint = $this->url ?? ($_SERVER['REQUEST_URI'] ?? '/');
        if (!is_string($endpoint) || !str_starts_with($endpoint, '/') || str_starts_with($endpoint, '//')) {
            throw new \InvalidArgumentException('Event URL must be a same-origin path beginning with /.');
        }
        $config = array('endpoint' => $endpoint, 'mode' => $this->type, 'csrf' => $this->csrfToken);
        $json = json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        $html = '';
        if ($this->stylesheetUrl !== '') $html .= '<link rel="stylesheet" href="'.Html::escape($this->stylesheetUrl).'">';
        if ($this->filebase !== '') {
            foreach (array('plugins/fontawesome-free/css/all.min.css', 'dist/css/adminlte.min.css') as $path) {
                $html .= '<link rel="stylesheet" href="'.Html::escape(rtrim($this->filebase, '/').'/'.$path).'">';
            }
            foreach (array('plugins/jquery/jquery.min.js', 'plugins/bootstrap/js/bootstrap.bundle.min.js', 'dist/js/adminlte.min.js') as $path) {
                $html .= '<script defer src="'.Html::escape(rtrim($this->filebase, '/').'/'.$path).'"></script>';
            }
        }
        return $html.'<script type="application/json" id="hiequeryui-config">'.$json.'</script><script defer src="'.Html::escape($this->runtimeUrl).'"></script>';
    }

    protected function receive(): ?array {
        if (!isset($_REQUEST['source']) && !isset($_REQUEST['event']) && !isset($_REQUEST['data']) && !isset($_POST['aj'])) return null;
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') return array(405, 'method', 'Widget events require POST.');
        foreach (array('source', 'event', 'data', 'csrf') as $field) {
            if (!isset($_POST[$field]) || !is_string($_POST[$field])) return array(400, 'request', 'Incomplete widget request.');
        }
        if (!hash_equals($this->csrfToken, $_POST['csrf'])) return array(403, 'session', 'The page session changed. Reload before continuing.');
        $widget = Widget::$WidgetDB[$_POST['source']] ?? null;
        if (!$widget instanceof InteractableWidget) return array(410, 'widget', 'This control is no longer available. Reload the page.');
        if (!$widget->canHandle($_POST['event'])) return array(400, 'event', 'Unsupported widget event.');
        if (strlen($_POST['data']) > 1048576 || base64_decode($_POST['data'], true) === false) return array(400, 'data', 'Invalid widget data.');
        $widget->dispatch($_POST['event'], $_POST['data']);
        return null;
    }

    public function updates(): array {
        $updates = array();
        $renders = 0;
        while (Widget::$changed) {
            if (++$renders > 1000) throw new \RuntimeException('Widgets keep changing during rendering.');
            $widget = reset(Widget::$changed);
            unset(Widget::$changed[$widget->getId()]);
            $updates[$widget->getId()] = base64_encode($widget->show());
        }
        return $updates;
    }

    public function start() {
        self::$me = $this;
        try {
            $error = $this->receive();
            if ($error) {
                http_response_code($error[0]);
                header('Cache-Control: no-store');
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array('error' => array('code' => $error[1], 'message' => $error[2])), JSON_THROW_ON_ERROR);
                return;
            }
            header('Cache-Control: no-store');
            if (isset($_POST['aj']) && $this->type !== 'full') {
                header('Content-Type: application/json; charset=utf-8');
                // Preserve the original array-of-maps response format.
                echo json_encode(array((object)$this->updates()), JSON_THROW_ON_ERROR);
                return;
            }
            header('Content-Type: text/html; charset=utf-8');
            $body = $this->entrypoint->show();
            if ($this->type === 'embedded') return $body;
            echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>'.Html::escape($this->title).'</title>'.$this->assets().'</head>';
            echo $this->entrypoint instanceof LTEAdminUI ? $body : '<body>'.$body.'</body>';
            echo '</html>';
        } finally { self::$me = false; }
    }
}
