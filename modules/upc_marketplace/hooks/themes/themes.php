<?php

/**
 * Implements hook_theme().
 */
function upc_marketplace_theme() {
  return [
    'upc_marketplace' => [
      'render element' => 'children',
    ],
  ];
}