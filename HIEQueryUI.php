<?php 
namespace HIEQueryUI;

include('interfaces/onChange.php');
include('interfaces/onClick.php');
include('interfaces/onTimeout.php');
include('interfaces/onEnter.php');

include('widgets/Widget.php');
include('widgets/InteractableWidget.php');
include('widgets/Container.php');
include('widgets/Button.php');
include('widgets/Checkbox.php');
include('widgets/Viewport.php');

include('widgets/Input.php');

include('widgets/NavItem.php');
include('widgets/Navbar.php');
include('widgets/LTEAdminUI.php');

class HIEQueryUI {
    public static $me = false;
    
    public $url;
    public $type;
    public $filebase;
    
    //possible types: embedded, full, ajax
    public $entrypoint;
    
    public function __construct($entrypoint,$type='full') {
        $this->entrypoint = $entrypoint;
        $this->type = $type;
        switch($type) {
            case 'embedded':
                
                break;
            case 'full':
                
                break;
            case 'ajax':
                
                break;
            
        }
    }
    
    public function start() {
        self::$me = $this;
        $ht = "";
        if (isset($_REQUEST["source"])&&isset($_REQUEST["event"])&&isset($_REQUEST["data"])) {
            $s = $_REQUEST["source"]."";
            $e = $_REQUEST["event"]."";
            $d = $_REQUEST["data"]."";
            Widget::$WidgetDB[$s]->$e($d);
            
        }
        if($this->type == "full" || (!isset($_REQUEST["aj"]) && $this->type=="ajax")) {
            $ht .= '
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>'.$this->title.'</title>
  <link rel="stylesheet" href="'.$this->filebase.'plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="'.$this->filebase.'dist/css/adminlte.min.css">
<!-- jQuery -->
<script src="'.$this->filebase.'plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="'.$this->filebase.'plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="'.$this->filebase.'dist/js/adminlte.min.js"></script>';
            
            
         if($this->type == "full")
            $ht .= '
<script type="text/javascript">
var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
function delay(callback, ms) {
  var timer = 0;
    
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}
function eventcall(source,action){
	window.location.replace("/?source="+source+"&event="+action+"&data="+Base64.encode($("#"+source).val()));
}

</script>';
            if($this->type == "ajax") {
                $ht .= ' 
<script type="text/javascript">
var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}

var timer = 0;
function deventcall(source, action) {
    if(event.keyCode == 13) {
        clearTimeout(timer);
        $.getJSON("/?source="+source+"&event=onEnter&aj=an&data="+Base64.encode($("#"+source).val()+""), function (data) {
            data.forEach(function item(item) {
                for (var key of Object.keys(item)) {
                    $("#"+key).replaceWith(Base64.decode(item[key]));
                }
            });
        });
    } else {
        clearTimeout(timer);
        timer = setTimeout(function(){eventcall(source,action)}, 500);
    }
}

function eventcall(source,action){
    if($("#"+source).is(":checkbox")) {
        var xar = $("#"+source).is(":checked");
        if(xar==false) {
            xar = "";
        }
        $.getJSON("/?source="+source+"&event="+action+"&aj=an&data="+Base64.encode(xar+""), function (data) {
                data.forEach(function item(item) {
                    for (var key of Object.keys(item)) {
                        $("#"+key).replaceWith(Base64.decode(item[key]));
                    }
                });
            });
    } else {
        $.getJSON("/?source="+source+"&event="+action+"&aj=an&data="+Base64.encode($("#"+source).val()+""), function (data) {
                data.forEach(function item(item) {
                    for (var key of Object.keys(item)) {
                        $("#"+key).replaceWith(Base64.decode(item[key]));
                    }
                });
            });
    }    
}


</script>
';
                    
     /*
      *     Object.keys(data).forEach(function(key, index) {
alert(data[index]);
    $("#"+key).replaceWith(Base64.decode(data[index]));
    });

      */           
            }
            
        
            
            $ht .= '</head>';
            $ht .= $this->entrypoint->show();   
            $ht .= '</html>';
        }
        if ($this->type == "ajax" && isset($_REQUEST["aj"])) {
            $ret = array();
            while(count(Widget::$changed)) {
                $w = array_pop(Widget::$changed);
                $ret[$w->getId()] = base64_encode($w->show());
            }
            header('Content-Type: application/json');
            echo "[".json_encode($ret)."]";
            return;
        }
        
        
        self::$me = false;
        if($this->type == "embedded") return $this->entrypoint->show();
        echo $ht;
    }
    
    public function getTransmitter() {
        
    }
    

    
}
?>