<?php

namespace Drupal\localcdn\Source;

interface SourceInterface {

  /**
   * @return string[]
   */
  public function getPrefixes(): array;

  /**
   * @param string[] $urls
   *   Format: $[] = $url_with_query
   *
   * @return string[]
   *   Format: $[$url] = $suffix
   */
  public function urlsGetSuffixes(array $urls): array;

  /**
   * @param string $suffix
   *
   * @return string|null
   */
  public function buildSourceUrl(string $suffix): ?string;

  /**
   * @param string $suffix
   *
   * @return string[]
   */
  public function getHeaders(string $suffix): array;

}
