<?php

include "route.php";

/**
 * -----------------------------------------------
 * PHP Route Things
 * -----------------------------------------------
 */

//define your route. This is main page route. for example www.example.com
Route::add('/', function(){
    //define which page you want to display while user hit main page.
    include('main.php');
});

//method for execution routes
Route::submit();
