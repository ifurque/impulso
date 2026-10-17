<?php

if (extension_loaded('mbstring')) {
    return;
}

require_once __DIR__ . '/../vendor/symfony/polyfill-mbstring/bootstrap.php';
