<?php

use Drupal\localcdn\Source\Source_GenericExternal;

function hook_localcdn_info() {
  $info = [];
  $info['jsdelivr'] = new Source_GenericExternal(
    'https://cdn.jsdelivr.net/',
    [
      'cache-control' => 'public, max-age=31536000, s-maxage=31536000',
      'content-encoding' => 'br',
    ]);
  return $info;
}
