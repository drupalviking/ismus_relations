<?php

/**
 * @file
 * Contains ismus_relation.page.inc.
 *
 * Page callback for Ismus relation entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Ismus relation templates.
 *
 * Default template: ismus_relation.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_ismus_relation(array &$variables) {
  // Fetch IsmusRelation Entity Object.
  $ismus_relation = $variables['elements']['#ismus_relation'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
