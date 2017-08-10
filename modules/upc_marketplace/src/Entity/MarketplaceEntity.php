<?php

namespace Drupal\upc_marketplace\Marketplace\Entity;

use Drupal\Core\Annotation\PluralTranslation;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\Annotation\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;

/**
 * @package UltimateSoftware\UltiproConnect\Marketplace\Entity
 * Class MarketplaceEntity
 * Define Marketplace Entity
 *
 * @ContentEntityType(
 *   id= "Marketplace",
 *   label = @Translation("Marketplace item"),
 *   label_singular = @Translation("marketplace item"),
 *   label_plural = @Translation("marketplace items"),
 *   label_count = @PluralTranslation(
 *      singular= "@count marketplace item",
 *      plural = "@count marketplace items'
 *    ),
 *   bundle_lable = @Translation("Marketplace"),
 *   handlers = {
 *      "view_builder" = "Drupal\UltimateSoftware\UltiproConnect\Marketplace\Entity\ViewBuilder\MarketplaceViewBuilder",
 *      "view_data" = "Drupal\UltimateSoftware\UltiproConnect\Marketplace\Entity\Views\MarketplaceViewData",
 *      "list_builder" = "Drupal\UltimateSoftware\UltiproConnect\Marketplace\Entity\Controller\MarketplaceListBuilder",
 *      "routes_provier" = {
 *            "html" = "Drupal\UltimateSoftware\UltiproConnect\Marketplace\Entity\Routing\MarketplaceRouteProvicer",
 *        },
 *      "form" = {
 *          "add" = "Drupal\UltimateSoftware\UltiproConnect\Marketplace\Entity\Form\MarkeplaceForm",
 *          "edit" = "Drupal\UltimateSoftware\UltiproConnect\Marketplace\Entity\Form\MarkeplaceForm",
 *          "delete"= "Drupal\UltimateSoftware\UltiproConnect\Marketplace\Entity\Form\MarkeplaceDeleteForm",
 *        },
 *      "access" = "Drupal\UltimateSoftware\UltiproConnect\Marketplace\Entity\Access\MarketplaceAccessControlHandler"
 *   }
 * )
 */
class MarketplaceEntity  extends ContentEntityBase implements MarketplaceInterface {
  use EntityChangedTrait;

}