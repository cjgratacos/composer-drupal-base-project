<?php

namespace Drupal\upc_marketplace\Entity;

use Drupal\user\UserInterface;

interface MarketplaceElementEntityInterface {

  /**
   * @return null|string
   */
   function getName():?string ;

  /**
   * @param $name
   *
   * @return \Drupal\upc_marketplace\Entity\MarketplaceElementEntityInterface
   */
  function setName($name) : MarketplaceElementEntityInterface;

  /**
   * @return null|string
   */
  function getCreatedTime():? string ;

  /**
   * @param $timestamp
   *
   * @return \Drupal\upc_marketplace\Entity\MarketplaceElementEntityInterface
   */
  function setCreatedTime($timestamp):MarketplaceElementEntityInterface;

  /**
   * @return \Drupal\user\UserInterface
   */
  function getOwner(): UserInterface;

  /**
   * @return int|null
   */
   function getOwnerId(): ?int;

  /**
   * @param $uid
   *
   * @return \Drupal\upc_marketplace\Entity\MarketplaceElementEntityInterface
   */
   function setOwnerId($uid): MarketplaceElementEntityInterface;

  /**
   * @param \Drupal\user\UserInterface $account
   *
   * @return \Drupal\upc_marketplace\Entity\MarketplaceElementEntityInterface
   */
  function setOwner(UserInterface $account): MarketplaceElementEntityInterface;

  /**
   * @return bool
   */
  function isPublished():bool;

  /**
   * @param $published
   *
   * @return \Drupal\upc_marketplace\Entity\MarketplaceElementEntityInterface
   */
  function setPublished($published): MarketplaceElementEntityInterface;

  /**
   * @return null|string
   */
  function getType():?string;
}