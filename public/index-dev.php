<?php
if(is_file("./".$_SERVER['REQUEST_URI'])){
    readfile("./".$_SERVER['REQUEST_URI']);
    exit;
}
if(is_file("./".$_SERVER['REQUEST_URI'].'/index.html')){
    readfile("./".$_SERVER['REQUEST_URI'].'/index.html');
    exit;
}
require "index.php";
