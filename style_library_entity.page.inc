<?php

/**
 * @file
 * Contains style_library_entity.page.inc.
 *
 * Page callback for Style Library entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Style Library templates.
 *
 * Default template: style_library_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_style_library_entity(array &$variables) {
  // Fetch StyleLibraryEntity Entity Object.
  $style_library_entity = $variables['elements']['#style_library_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
