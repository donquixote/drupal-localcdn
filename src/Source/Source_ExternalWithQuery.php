<?php

namespace Drupal\localcdn\Source;

class Source_ExternalWithQuery implements SourceInterface {

  /**
   * @var string
   */
  private $prefix;

  /**
   * @var array
   */
  private $headers;

  /**
   * @var string|null
   */
  private $fileExtension;

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
   * @param string $extension
   *
   * @return static
   */
  public function withFileExtension(string $extension) {
    $clone = clone $this;
    $clone->fileExtension = $extension;
    return $clone;
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
    $urls_filtered = preg_grep('@^' . preg_quote($this->prefix, '@') . '@', $urls);
    $suffixes = [];
    foreach ($urls_filtered as $url) {
      if (0 !== strpos($url, $this->prefix)) {
        continue;
      }
      $relative_url = substr($url, \strlen($this->prefix));
      $relative_url_parsed = parse_url($relative_url);
      $suffix = urlencode($relative_url_parsed['query']) . '/' . $relative_url_parsed['path'];
      if (null !== $this->fileExtension) {
        $suffix .= $this->fileExtension;
      }
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
    list($query_string_encoded, $relative_path) = explode('/', $suffix, 2) + ['', ''];
    if ('' === $relative_path) {
      return null;
    }
    if (null !== $this->fileExtension) {
      if ($this->fileExtension !== substr($relative_path, -\strlen($this->fileExtension))) {
        return null;
      }
      $relative_path = \substr($relative_path, 0, -\strlen($this->fileExtension));
    }
    $path = $this->prefix . $relative_path;
    $options = ['external' => TRUE];
    if ('' !== $query_string_encoded) {
      $query_string_decoded = urldecode($query_string_encoded);
      parse_str($query_string_decoded, $query);
      if ([] !== $query ?? []) {
        $options['query'] = $query;
      }
    }
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
    switch ($ext = $info['extension'] ?? '') {
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
