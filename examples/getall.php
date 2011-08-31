<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .
             'src' . DIRECTORY_SEPARATOR .
             'Services' . DIRECTORY_SEPARATOR .
             'Mongolab' . DIRECTORY_SEPARATOR . 'Partner.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

use orchestra\services\mongolab\Partner;

$svc = new Partner($conf['account-name']);
$svc->setAuth($conf['username'], $conf['password']);
$accounts = $svc->getAll();

print_r($accounts); die();
