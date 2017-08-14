<?php

namespace Drupal\upc_marketplace\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Marketplace Element Entity entities.
 */
class MarketplaceElementEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
