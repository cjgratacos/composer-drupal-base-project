<?php

/**
 * @file
 * Contains mp_elem_ent.page.inc.
 *
 * Page callback for Marketplace Element Entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Marketplace Element Entity templates.
 *
 * Default template: mp_elem_ent.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_mp_elem_ent(array &$variables) {
  // Fetch MarketplaceElementEntity Entity Object.
  $mp_elem_ent = $variables['elements']['#mp_elem_ent'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}