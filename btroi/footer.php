<?php

$rate_limit = api_url_to_array('https://api.github.com/rate_limit');
$core_rate_limit = $rate_limit['resources']['core'];

echo '
        <span style="font-size: 12px; color: #555555;">
          GitHub rate-limited requests remaining: ' . $core_rate_limit['remaining'] . '/' . $core_rate_limit['limit'] . '
        </span>
      </div>
      <!-- End page content-->

    <p style="clear:both;"></p>

    <div style="height: 1px; margin: 0px 50px 0px 50px; background-color: #AAAAAA;"></div>
    <div class="style_me" style="text-align: center">
      Benjamin Fenner<br>
      BoomTown Technical Assessment<br>
      Copyright &#169; 2020-' . date('Y') . ' Fork Computing
    </div>
    <div style="height: 1px; margin: 0px 50px 0px 50px; background-color: #AAAAAA;"></div>
    </body>
  </html>';