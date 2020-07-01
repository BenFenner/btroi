<?php

/////////////////////////////////////////////////////////
//
// Time Zone setup
//
/////////////////////////////////////////////////////////
date_default_timezone_set('America/New_York');



/////////////////////////////////////////////////////////
//
// Site-wide array constants
//
// Array constants have a complicated history.
// See: https://stackoverflow.com/questions/1290318/php-constants-containing-arrays
//
/////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////
//
// Site-wide scalar constants
//
/////////////////////////////////////////////////////////
if (!defined('BACKGROUND_COLOR')) {
  // All of the constants are inside this check.
  // No need to define them again if they already exist.

  // Look and feel
  define('BACKGROUND_COLOR', '#BDD2FD');
  define('RESULTS_PER_PAGE', '30');

  // Directories
  define ('ROOT_URL', (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST']);
  
  // URLs
  define ('API_ENDPOINT', 'https://api.github.com/orgs/BoomTownROI');
  define ('FOLLOW_URL', 'api.github.com/orgs/BoomTownROI');
}



/////////////////////////////////////////////////////////
//
// Session variables
//
/////////////////////////////////////////////////////////
if (session_status() == PHP_SESSION_NONE) {
  session_start();
  $_SESSION['breadcrumbs'] = array();  // Array "stack" used to show the hierarchy of retrieved API resources.
}



/////////////////////////////////////////////////////////
//
// Site-wide functions
//
/////////////////////////////////////////////////////////
if (!function_exists('array_useful')) {
  // All of the functions are inside this check.
  // No need to instantiate them again if they already exist.

  // Take an array and return if it is useful.
  // Useful arrays are those that contain at least one element.
  // Useless arrays are NULL, empty, or non-array structures.
  //
  // This function makes use of pass-by-reference so if an array index is passed in
  // it will not be evaluated until we know it is set. This avoids possible array
  // index parse errors.
  function array_useful(&$potential_array) {
    if (!isset($potential_array)) { return FALSE; }

    $array = $potential_array;  // Drop our reference to the passed in parameter so we don't accidentally modify it later.
    if (!is_array($array)) { return FALSE; }
    if (empty($array))     { return FALSE; }

    return TRUE;
  }

  // Take a sring and return if it is useful.
  // Useful strings are those that contain at least one non-whitespace character.
  // Useless strings are NULL, empty, or contain all whitespace.
  //
  // To check for all known whitespace, this function makes use of a regex cribbed
  // from: https://stackoverflow.com/a/40048457
  //
  // This function makes use of pass-by-reference so if an array index is passed in
  // it will not be evaluated until we know it is set. This avoids possible array
  // index parse errors.
  function string_useful(&$potential_string) {
    if (!isset($potential_string)) { return FALSE; }

    $string = $potential_string;  // Drop our reference to the passed in parameter so we don't accidentally modify it later.
    if (strlen($string) < 1) { return FALSE; }

    $no_whitespace = preg_replace("/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/",
                                  '',
                                  $string);

    if (strlen($no_whitespace) < 1) { return FALSE; }

    return TRUE;
  }
}
