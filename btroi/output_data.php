<?php

$title = 'BoomTown - Technical Assessment - Output Data';

include('header.php');


// Connect to BoomTown's GitHub API endpoint and consume the first JSON object as an array.
$elements = array();
try {
  $elements = api_url_to_array(API_ENDPOINT);
} catch (Exception $e) { output_rate_limit_notice($e); }

// The top-level data does not appear to be recursive, so a loop should suffice.
foreach($elements as $element) {
  if (strpos($element, FOLLOW_URL) !== FALSE) {
    // The array element represents a potential API URL path, so follow the URL and display
    // all 'id' keys/values in the response.

    $page_count = get_page_count($element);
    for ($i = 1; $i <= $page_count; $i++) {
      $paginated_url = $element . '?page=' . $i . '&per_page=' . RESULTS_PER_PAGE;
      echo '<b><a href="' . $paginated_url . '">' . $element . '</a></b> (Page ' . $i . ' of ' . $page_count . ')<br>';

      $response = array();
      try {
        $response = api_url_to_array($paginated_url);
      } catch (Exception $e) { output_rate_limit_notice($e); }

      if (!$response) {
        echo 'No data found.<br>';
      } else {
        $_SESSION['breadcrumbs'] = array();
        output_api_resourses($response);
      }
      
      echo '<br>';
    }

    echo '<br><br>';
  }
}


include('footer.php');



/////////////////////////////////////////////////////////
//
// Functions
//
/////////////////////////////////////////////////////////

// Recursive function designed to output all of the 'id' contents of an API resource.
function output_api_resourses($resource) {
  foreach($resource as $key => $value) {
    if (array_useful($value)) {
      array_push($_SESSION['breadcrumbs'], $key);
      output_api_resourses($value);
    } elseif ($key == 'id') {
      echo output_breadcrumbs() . $key . ': ' . $value . '<br>';
    }
  }
  array_pop($_SESSION['breadcrumbs']);
}

// Output breadcrumbs to communicate API resource hierarchy.
function output_breadcrumbs() {
  $delimeter = ' &#8594; ';   // The breadcrumb delimeter contains a unicode rightwards arrow.

  echo '<span style="color: #858585;">';
  foreach ($_SESSION['breadcrumbs'] as $crumb) {
    echo $crumb . $delimeter;
  }
  echo '</span>';
}