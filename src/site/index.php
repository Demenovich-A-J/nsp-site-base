<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

include "route.php";

/**
 * -----------------------------------------------
 * PHP Route Things
 * -----------------------------------------------
 */

//define your route. This is main page route. for example www.example.com
Route::add('/', function(){
    //define which page you want to display while user hit main page.
    include('pages/main/main.php');
});

//define your route. This is main page route. for example www.example.com
Route::add('/discount', function(){
    //define which page you want to display while user hit main page.
    include('handlers/discount-form-handler.php');
});

//method for execution routes
Route::submit();
