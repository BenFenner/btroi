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

  // Given an API URL, return the contents of the URL as an assosiative array.
  // If the response is not an HTTP code 200, return false. Throw an exception
  // if the GitHub rate limit is reached.
  function api_url_to_array($url) {
    $curl_handle = curl_init($url);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'BenFenner');
    $response_string = curl_exec($curl_handle);

    $curl_error = curl_errno($curl_handle);
    $http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
    curl_close($curl_handle);

    $response = json_decode($response_string, TRUE);

    if ($http_code == 403 && substr($response['message'], 0, 27) == 'API rate limit exceeded for') {
      throw new Exception('GitHub hourly rate limit reached. All execution halted. Please try again in about an hour.');
    }

    if ($http_code != 200) {
      return FALSE;
    }

    return $response;
  }

  // Output a notice about exceeding the GitHub hourly rate limit, display
  // the footer, then halt execution.
  function output_rate_limit_notice($e) {
    echo '
      <br><br>
      <span style="font-weight: bold; color: red;">' . $e->getMessage() . '</span><br>';

    include('footer.php');
    die();
  }

  // Given a GitHub API endpoint URL, return the number of pages that exist for the records.
  // If looking up the page count fails, assume a single page.
  //
  // This function should scale well as page count grows, requiring only one API
  // call O(1) asking for header data only for an infinite amount of records.
  function get_page_count($url) {
    $page_count = 1;

    // Get the headers, so we can parse out the "link" section containing the final page number.
    $paginated_url = $url . '?page=1&per_page=' . RESULTS_PER_PAGE;
    $context = stream_context_create(array('http' => array('method' => 'GET',
                                                           'header' => 'User-Agent: BenFenner')));
    $headers = get_headers($paginated_url, 1, $context);

    if (string_useful($headers['link'])) {
      // There is useful "link" header data, so determine the page count from it.

      // The link variable below will contain a string that looks something like this:
      // <https://api.github.com/organizations/1214096/repos?page=2&per_page=30>; rel="next", <https://api.github.com/organizations/1214096/repos?page=2&per_page=30>; rel="last"
      //
      // We are only interested in the page listed as the last, so use a bit of string manipulation to get it.
      // TODO: There are probably better/cleaner ways to accomplish this, so look into improvements.
      $link = $headers['link'];

      $begin_phrase = '&per_page=' . RESULTS_PER_PAGE . '>; rel="next", <';
      $end_phrase   = '&per_page=' . RESULTS_PER_PAGE . '>; rel="last"';
      $begin_index  = strpos($link, $begin_phrase) + strlen($begin_phrase);
      $end_index    = strrpos($link, $end_phrase);
      $length       = $end_index - $begin_index;

      // The finale_page_url variable below should contain a string that looks something like this:
      // https://api.github.com/organizations/1214096/repos?page=2
      $final_page_number_url = substr($link, $begin_index, $length);

      $phrase = '?page=';
      $index = strrpos($final_page_number_url, $phrase) + strlen($phrase);

      $page_count = substr($final_page_number_url, $index);
    }

    return $page_count;
  }
}
