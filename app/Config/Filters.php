<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use App\Filters\Oauth2Filter;
use App\Filters\UserFilter;
use App\Filters\ClientFilter;
use App\Filters\FinancialFilter;
use App\Filters\EditorFilter;
use App\Filters\ManagerFilter;

class Filters extends BaseConfig
{
	/**
	 * Configures aliases for Filter classes to
	 * make reading things nicer and simpler.
	 *
	 * @var array
	 */
	public $aliases = [
		'csrf'     => CSRF::class,
		'toolbar'  => DebugToolbar::class,
		'honeypot' => Honeypot::class,
        'oauth2Filter' => Oauth2Filter::class,
        'userFilter' => UserFilter::class,
        'financialFilter' => FinancialFilter::class,
        'editorFilter' => EditorFilter::class,
        'managerFilter' => ManagerFilter::class,
        'clientFilter' => ClientFilter::class
	];

	/**
	 * List of filter aliases that are always
	 * applied before and after every request.
	 *
	 * @var array
	 */
	public $globals = [
		'before' => [
		    'oauth2Filter'
			// 'honeypot',
			// 'csrf',
		],
		'after'  => [
			'toolbar',
			// 'honeypot',
		],
	];

	/**
	 * List of filter aliases that works on a
	 * particular HTTP method (GET, POST, etc.).
	 *
	 * Example:
	 * 'post' => ['csrf', 'throttle']
	 *
	 * @var array
	 */
	public $methods = [];

	/**
	 * List of filter aliases that should run on any
	 * before or after URI patterns.
	 *
	 * Example:
	 * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
	 *
	 * @var array
	 */
	public $filters = [
	    'oauth2Filter' => ['before' => [
            'service',
	        'service/*',
            'client',
            'client/*',
            'user/login',
            'user/register',
            'invoice/*'
        ]],
        'managerFilter' => ['before' => [
            'service',
            'service/*',
            'user/register',
            'user',
        ]],
        'financialFilter' => ['before' => [
            'invoice/*',
            'invoice'
        ]],
        'editorFilter' => ['before' => [
            'client/*',
            'client'
        ]],
        'clientFilter' => ['before' => [
            'customer/invoice/*',
            'customer/invoice'
        ]]
    ];
}
