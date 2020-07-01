<?php

  include(__DIR__ . '/common.php');

  $meta_description = string_useful($meta_description) ? $meta_description : $title;

  echo '
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >

    <head>
      <base href="' . ROOT_URL . '/" />
      <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
      <meta http-equiv="content-language" content="en" />
      <meta name="language" content="en" />
      <meta name="description" content="' . $meta_description . '" />
      <link rel="stylesheet" type="text/css" href="' . ROOT_URL . '/btroi/styles/main.css" media="screen" title="screen" />
      <link rel="shortcut icon" type="image/png" href="https://2lnopk3ltiuj1tkm8y4d7nfx-wpengine.netdna-ssl.com/wp-content/themes/boomtown-5/favicon.ico?v=5" />
      <meta name="keywords" content="BoomTown ROI, BoomTown, Real Estate Marketing, Real Estate Technology" />
      <meta property="og:url" content="' . ROOT_URL . $_SERVER['REQUEST_URI'] . '" />
      <meta property="og:image" content="' . ROOT_URL . '/btroi/images/open_graph_logo.png" />
      <meta property="og:image:alt" content="BoomTown Logo" />
      <meta property="og:title" content="' . $title . '" />
      <meta property="og:description" content="' . $meta_description . '" />
      <meta property="og:site_name" content="BTROI Technical Assessment" />
      <meta property="og:type" content="website" />
      <title>' . $title . '</title>
    </head>

    <body>

      <br>

      <div class="hr"></div>
      <div class="title">' . $title . '</div>
      <div class="hr"></div>
      <div class="navigation">
        <a href="btroi/home.php">Home</a>
        |
        <a href="btroi/output_data.php">Output Data</a>
        |
        <a href="btroi/perform_verifications.php">Perform Verifications</a>
      </div>

      <!-- Begin page content-->
      <a href="#top"></a>
      <div class="main_content">';