<?php

return [

	'debug' => env('APP_DEBUG'),

	'timezone' => 'UTC',

	'locale' => 'en',

	'fallback_locale' => 'en',

	'key' => env('APP_KEY', 'SomeRandomString42'),

	'cipher' => MCRYPT_RIJNDAEL_128,

	'log' => 'daily',

	'providers' => [
		'Illuminate\Foundation\Providers\ArtisanServiceProvider',
		'Illuminate\Cache\CacheServiceProvider',
		'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider',
		'Illuminate\Database\DatabaseServiceProvider',
		'Illuminate\Filesystem\FilesystemServiceProvider',

	    'Maatwebsite\Excel\ExcelServiceProvider',
	],

	'aliases' => [

		'App'       => 'Illuminate\Support\Facades\App',
		'Artisan'   => 'Illuminate\Support\Facades\Artisan',
		'Cache'     => 'Illuminate\Support\Facades\Cache',
		'Config'    => 'Illuminate\Support\Facades\Config',
		'DB'        => 'Illuminate\Support\Facades\DB',
		'Eloquent'  => 'Illuminate\Database\Eloquent\Model',
		'File'      => 'Illuminate\Support\Facades\File',
		'Log'       => 'Illuminate\Support\Facades\Log',
		'Storage'   => 'Illuminate\Support\Facades\Storage',

		'Response'  => 'Illuminate\Support\Facades\Response',

		'Excel'     => 'Maatwebsite\Excel\Facades\Excel',
	],

];
