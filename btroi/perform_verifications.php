<?php

$title = 'BoomTown - Technical Assessment - Perform Verifications';

include('header.php');


// Connect to BoomTown's GitHub API endpoint and consume the first JSON object as an array.
$elements = array();
try {
  $elements = api_url_to_array(API_ENDPOINT);
} catch (Exception $e) { output_rate_limit_notice($e); }

// Verify that the updated_at date is later than the created_at date.
$created_at = date_create($elements['created_at']);
$updated_at = date_create($elements['updated_at']);
$verify_chronological = datetimes_are_chronological($created_at, $updated_at);
echo 'Created at: ' . $created_at->format('Y-m-d H:i:s') . '<br>';
echo 'Updated at: ' . $updated_at->format('Y-m-d H:i:s') . '<br>';
echo 'Datetime chronology verification: ' . $verify_chronological . '<br><br>';

// Verify that the public repo count matches the number of actual public repos.
$listed_count = $elements['public_repos'];
$actual_count = get_record_count($elements['repos_url']);
$verify_repo_count = repo_counts_match($listed_count, $actual_count);

echo 'Listed public repository count: ' . $listed_count . '<br>';
echo 'Actual public repository count: ' . $actual_count . '<br>';
echo 'Public repository verification: ' . $verify_repo_count;

echo '<br><br>';

include('footer.php');



/////////////////////////////////////////////////////////
//
// Functions
//
/////////////////////////////////////////////////////////

// Given a GitHub API endpoint URL, return the number of records it contains, making
// use of pagination if needed.
//
// This function should scale well as page count grows, requiring only two API
// calls O(2) (one of them asking for header data only) for an infinite amount of records
// instead of the classic, brute-force technique that would be O(n).
// This is accomplished by working with the header data to determine the final page number.
// From there we can assume the count of all previous pages, and only need to count the
// records contained within the final page.
function get_record_count($url) {
  $final_page_number = get_page_count($url);

  // Get the record count for the final page (we only have to count results from this page, the rest can be calculated).
  $final_page_url = $url . '?page=' . $final_page_number . '&per_page=' . RESULTS_PER_PAGE;
  $records = array();
  try {
    $records = api_url_to_array($final_page_url);
  } catch (Exception $e) { output_rate_limit_notice($e); }

  return (($final_page_number - 1) * RESULTS_PER_PAGE) + count($records);
}

// Given two datetimes, return the appropriate HTML-formatted string based on their chronology.
function datetimes_are_chronological($first, $second) {
  $result = '<span style="color: red">The datetimes are not chronological.</span>';

  if ($first == $second) {
    $result = '<span style="color: orange">The datetimes are identical.</span>';
  } elseif ($first < $second) {
    $result = '<span style="color: green">The datetimes are chronological.</span>';
  }

  return $result;
}

// Given two integers, return the appropriate HTML-formatted string based on their comparison.
function repo_counts_match($listed, $actual) {
  $result = '<span style="color: red">There are more public repositories listed in the count than there are actual repositories.</span>';

  if ($listed < $actual) {
    $result = '<span style="color: red">There are fewer public repositories listed in the count than there are actual repositories.</span>';
  } elseif ($listed == $actual) {
    $result = '<span style="color: green">The listed public repository count matches the actual number of public repostories.</span>';
  }

  return $result;
}