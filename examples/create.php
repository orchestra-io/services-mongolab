<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .
             'src' . DIRECTORY_SEPARATOR .
             'Services' . DIRECTORY_SEPARATOR .
             'Mongolab' . DIRECTORY_SEPARATOR . 'Partner.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

use orchestra\services\mongolab\Partner;

$svc = new Partner($conf['account-name']);
$svc->setAuth($conf['username'], $conf['password']);

// Create a new account
$userId = 'username' . mt_rand(2,100);

$user = $svc->create(array(
    'name'      => $userId,
    'adminUser' => array(
        'email' => 'user@email.com',
    )
));

echo 'Username: ' . $user->adminUser->email . PHP_EOL;
echo 'Password: ' . $user->adminUser->password . PHP_EOL;

$username = $user->adminUser->email;
$password = $user->adminUser->password;


// Provision a new database
$result = $svc->addDatabase($conf['account-name'] . '_' . $userId, array(
    'name'     => $conf['account-name'] . '_' . $userId . '_' . sha1(time().time()),
    'plan'     => 'free',
    'username' => 'acme',
));

echo 'Database name: ' . $result->name . PHP_EOL;
echo 'Database URI: '  . $result->uri . PHP_EOL;
