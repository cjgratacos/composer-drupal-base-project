<?php

namespace Drupal\upc_marketplace;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Marketplace Element Entity entity.
 *
 * @see \Drupal\upc_marketplace\Entity\MarketplaceElementEntity.
 */
class MarketplaceElementEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\upc_marketplace\Entity\MarketplaceElementEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished marketplace element entity');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published marketplace element entity');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit marketplace element entity');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete marketplace element entity');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add marketplace element entity');
  }

}
