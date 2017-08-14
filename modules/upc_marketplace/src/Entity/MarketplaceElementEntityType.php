<?php

namespace Drupal\upc_marketplace\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Marketplace Element Entity type entity.
 *
 * @ConfigEntityType(
 *   id = "mp_elem_ent_type",
 *   label = @Translation("Marketplace Element Entity type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\upc_marketplace\MarketplaceElementEntityTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\upc_marketplace\Form\MarketplaceElementEntityTypeForm",
 *       "edit" = "Drupal\upc_marketplace\Form\MarketplaceElementEntityTypeForm",
 *       "delete" = "Drupal\upc_marketplace\Form\MarketplaceElementEntityTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\upc_marketplace\MarketplaceElementEntityTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "mp_elem_ent_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "mp_elem_ent",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/mp_elem_ent_type/{mp_elem_ent_type}",
 *     "add-form" = "/admin/structure/mp_elem_ent_type/add",
 *     "edit-form" = "/admin/structure/mp_elem_ent_type/{mp_elem_ent_type}/edit",
 *     "delete-form" = "/admin/structure/mp_elem_ent_type/{mp_elem_ent_type}/delete",
 *     "collection" = "/admin/structure/mp_elem_ent_type"
 *   }
 * )
 */
class MarketplaceElementEntityType extends ConfigEntityBundleBase implements MarketplaceElementEntityTypeInterface {

  /**
   * The Marketplace Element Entity type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Marketplace Element Entity type label.
   *
   * @var string
   */
  protected $label;

  /**
   *  The Marketplace Element Entity description.
   * @var string
   */
  protected $description;

  /**
   * @return string
   */
  public function getId(): ?string {
    return $this->id;
  }

  /**
   * @param string $id
   *
   * @return $this
   */
  public function setId(string $id): MarketplaceElementEntityTypeInterface {
    $this->id = $id;
    return $this;
  }

  /**
   * @return string
   */
  public function getLabel(): ?string {
    return $this->label;
  }

  /**
   * @param string $label
   *
   * @return \Drupal\upc_marketplace\Entity\MarketplaceElementEntityTypeInterface
   */
  public function setLabel(string $label): MarketplaceElementEntityTypeInterface {
    $this->label = $label;
    return $this;

  }

  /**
   * @return string
   */
  public function getDescription(): ?string {
    return $this->description;
  }

  /**
   * @param string $description
   *
   * @return $this
   */
  public function setDescription(string $description): MarketplaceElementEntityTypeInterface {
    $this->description = $description;
    return $this;
  }
}
