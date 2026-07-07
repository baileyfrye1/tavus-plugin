<?php

require_once __DIR__ . '/includes/tavus-bootstrap.php';

/*
 * 
 * Plugin Name: Tavus Integration
 * Description: Custom plugin to integrate Tavus with Tea on THC website
 * Author: Bailey Frye
 * Version: 0.1.1
 *
 */

if (!defined('ABSPATH')) {
    exit;
}

class Tavus_Main {
    public function __construct()
    {
        new Tavus_Bootstrap();
    }
}


new Tavus_Main;