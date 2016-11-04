<?php

define('ENV_DEVELOPMENT', 'dev');
define('ENV_TEST', 'test');
define('ENV_UAT', 'uat');
define('ENV_PRODUCTION', 'prod');

if (!defined('ENVIRONMENT')) {
    if (($environment = getenv('ENVIRONMENT')) == false || getenv('ENVIRONMENT') == ENV_PRODUCTION) {
        if (php_sapi_name() == 'cli') {
            if (strpos(__DIR__, 'C:') !== false) {
                $environment = ENV_DEVELOPMENT;
            } elseif (strpos(__DIR__, '/Users/') !== false) {
                $environment = ENV_DEVELOPMENT;
            } else {
                $environment = ENV_PRODUCTION;
            }
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            switch (@$_SERVER['SERVER_NAME']) {

                case 'localhost':
                case '127.0.0.1': {
                    $environment = ENV_DEVELOPMENT;
                    break;
                }
                case 'test': {
                    $environment = ENV_TEST;
                    break;
                }
                default: {
                    $environment = ENV_PRODUCTION;
                    break;
                }
            }

        } else {
            $environment = ENV_PRODUCTION;
        }
    }
    define('ENVIRONMENT', $environment);
}