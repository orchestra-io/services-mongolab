# Synopsis

This package is used to interact with the MongoLab provider API. As the API evolves, this package
will cover the new methods provided.

# Requirements

  - PHP 5.3.x
  - PECL HTTP 
  - A Partner account with MongoLab

# Example

Here are a few methods covered by this API wrapper.

## Create new Database

``` php
<?php 
    require_once 'Orchestra/Services/Mongolab/Partner.php'; 
    use orchestra\services\mongolab\Partner;

    $svc = new Partner('acme');
    $svc->setAuth('XXX', 'YYY');

    // Create a new account
    $user = $svc->create(array(
        'name'      => 'username1',
        'adminUser' => array(
            'email' => 'user@email.com',
        );
    ));

    echo 'Username: ' . $user->adminUser->email . PHP_EOL;
    echo 'Password: ' . $user->adminUser->password . PHP_EOL;
    
    $username = $user->adminUser->email;
    $password = $user->adminUser->password;

    // Provision a new database
    $result = $svc->addDatabase('acme_' . $user->name, array(
        'name'     => 'acme_username1_random666',
        'plan'     => 'free', 
        'username' => 'acmeusername',
    ));

    echo 'Database name: ' . $result->name . PHP_EOL;
    echo 'Database URI: ' . $result->uri . PHP_EOL;
?>
```


## Delete a previously created database
``` php
<?php 
    require_once 'Orchestra/Services/Mongolab/Partner.php'; 
    use orchestra\services\mongolab\Partner;

    $svc = new Mongolab('account-name');
    $svc->setAuth('XXX', 'YYY');
    $response = $svc->deleteDatabase('database-name');

    var_dump($response);
?>
```

# License

This is released under the New BSD license. Should you require a copy of the license, it is
included in this very repository.

# Copyright

Orchestra Platform Ltd. 2011

# Links

  - [Orchestra.io](https://orchestra.io)
  - [MongoLab](http://mongolab.com)


