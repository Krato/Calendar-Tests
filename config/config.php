<?php

return [

	/*
    |--------------------------------------------------------------------------
    | Format date as you want to show. Default to: YYYY-MM-DD HH:mm
    |--------------------------------------------------------------------------
    |
    */
	'formatDate' => 'YYYY-MM-DD HH:mm',

	/*
    |--------------------------------------------------------------------------
    | If you want to use cache set to true. But add some cache wich let tags
    |--------------------------------------------------------------------------
    |
    */
	'useCache' => false,

	/*
    |--------------------------------------------------------------------------
    | Default cache time. Only works if useCache option is true
    |--------------------------------------------------------------------------
    |
    */
	'cacheTime' => 10,

	/*
    |--------------------------------------------------------------------------
    | Model Bind for event Categories
    |--------------------------------------------------------------------------
    |
    */
	'modelBind' => App\User::class,

	/*
    |--------------------------------------------------------------------------
    | Model column to filter
    |--------------------------------------------------------------------------
    |
    */
	'modelColumn' => 'name',

	/*
    |--------------------------------------------------------------------------
    | Model label to show on form
    |--------------------------------------------------------------------------
    |
    */
	'modelLabel' => 'User'
];
