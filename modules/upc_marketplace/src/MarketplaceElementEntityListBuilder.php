<?php

namespace Drupal\upc_marketplace;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Marketplace Element Entity entities.
 *
 * @ingroup upc_marketplace
 */
class MarketplaceElementEntityListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Marketplace Element Entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\upc_marketplace\Entity\MarketplaceElementEntity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.mp_elem_ent.edit_form',
      ['mp_elem_ent' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
