<?php

namespace Drupal\localcdn\Source;

class Source_GenericExternal implements SourceInterface {

  /**
   * @var string
   */
  private $prefix;

  /**
   * @var array
   */
  private $headers;

  /**
   * Constructor.
   *
   * @param string $prefix
   * @param array $headers
   */
  public function __construct(string $prefix, array $headers = []) {
    $this->prefix = $prefix;
    $this->headers = $headers;
  }

  /**
   * @return string[]
   */
  public function getPrefixes(): array {
    return [$this->prefix];
  }

  /**
   * @param string[] $urls
   *   Format: $[] = $url_with_query
   *
   * @return string[]
   *   Format: $[$url] = $suffix
   */
  public function urlsGetSuffixes(array $urls): array {
    $pattern = '@^' . preg_quote($this->prefix, '@') . '@';
    $urls_filtered = preg_grep($pattern, $urls);
    $suffixes = [];
    foreach ($urls_filtered as $url) {
      if (0 !== strpos($url, $this->prefix)) {
        continue;
      }
      $suffix = substr($url, \strlen($this->prefix));
      $suffixes[$url] = $suffix;
    }
    return $suffixes;
  }

  /**
   * @param string $suffix
   *
   * @return string|null
   */
  public function buildSourceUrl(string $suffix): ?string {
    $path = $this->prefix . $suffix;
    $options = ['external' => TRUE];
    return url($path, $options);
  }

  /**
   * @param string $suffix
   *
   * @return string[]
   */
  public function getHeaders(string $suffix): array {
    $info = pathinfo($suffix);
    $headers = $this->headers;
    switch ($ext = $info['extension']) {
      case 'css':
        $headers['content-type'] = 'text/css; charset=utf-8';
        break;
      case 'js':
        $headers['content-type'] = 'application/javascript; charset=utf-8';
        break;
    }

    return $headers;
  }
}
