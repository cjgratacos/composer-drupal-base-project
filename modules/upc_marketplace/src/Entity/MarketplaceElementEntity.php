<?php

namespace Drupal\upc_marketplace\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Marketplace Element Entity entity.
 *
 * @ingroup upc_marketplace
 *
 * @ContentEntityType(
 *   id = "mp_elem_ent",
 *   label = @Translation("Marketplace Element Entity"),
 *   bundle_label = @Translation("Marketplace Element Entity type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\upc_marketplace\MarketplaceElementEntityListBuilder",
 *     "views_data" = "Drupal\upc_marketplace\Entity\MarketplaceElementEntityViewsData",
 *     "translation" = "Drupal\upc_marketplace\MarketplaceElementEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\upc_marketplace\Form\MarketplaceElementEntityForm",
 *       "add" = "Drupal\upc_marketplace\Form\MarketplaceElementEntityForm",
 *       "edit" = "Drupal\upc_marketplace\Form\MarketplaceElementEntityForm",
 *       "delete" = "Drupal\upc_marketplace\Form\MarketplaceElementEntityDeleteForm",
 *     },
 *     "access" = "Drupal\upc_marketplace\MarketplaceElementEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\upc_marketplace\MarketplaceElementEntityHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "mp_elem_ent",
 *   data_table = "mp_elem_ent_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer marketplace element entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/marketplace/element/{mp_elem_ent}",
 *     "add-page" = "/admin/marketplace/element/add",
 *     "add-form" = "/admin/marketplace/element/add/{mp_elem_ent_type}",
 *     "edit-form" = "/admin/marketplace/element/{mp_elem_ent}/edit",
 *     "delete-form" = "/admin/marketplace/element/{mp_elem_ent}/delete",
 *     "collection" = "/admin/marketplace/element/",
 *   },
 *   bundle_entity_type = "mp_elem_ent_type",
 *   common_reference_target = TRUE,
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.mp_elem_ent_type.edit_form"
 * )
 */
class MarketplaceElementEntity extends ContentEntityBase implements MarketplaceElementEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName():?string {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name):MarketplaceElementEntityInterface {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime():?string {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp): MarketplaceElementEntityInterface {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner():UserInterface{
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId():?int {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid): MarketplaceElementEntityInterface {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account):MarketplaceElementEntityInterface {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished(): bool {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published): MarketplaceElementEntityInterface{
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  public function getType(): ?string {
    return $this->bundle();
  }
  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Marketplace Element Entity entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the Marketplace Element Entity entity.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Type'))
      ->setDescription(t('The Marketplace-Element Type'))
      ->setSetting('target_type', 'mp_elem_ent_type')
      ->setReadOnly(true);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Marketplace Element Entity is published.'))
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
