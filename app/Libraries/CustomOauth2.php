<?php

namespace App\Libraries;

use \App\Libraries\CustomOauthStorage;

class CustomOauth2 {
    var $server;

    function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $dsn = getenv('database.default.DSN');
        $username = getenv('database.default.username');
        $password = getenv('database.default.password');

        $storage = new CustomOauthStorage(['dsn' => $dsn, 'username' => $username, 'password' => $password]);
        $this->server = new \OAuth2\Server($storage);
        $this->server->addGrantType(new \OAuth2\GrantType\UserCredentials($storage));
    }
}