<?php

namespace Drupal\upc_marketplace\Marketplace\Entity;


use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\user\EntityOwnerInterface;

interface MarketplaceInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface, RevisionLogInterface {

  public function getTitle(): string ;

  public function setTitle(string $title): MarketplaceInterface;

  public function getBody(): string ;

  public function setBody(string $body): MarketplaceInterface;

  public function isPublished():bool ;

  public function setPublished(bool $published): MarketplaceInterface;

}