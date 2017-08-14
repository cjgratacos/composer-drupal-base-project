<?php

namespace Drupal\upc_marketplace\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Marketplace Element Entity edit forms.
 *
 * @ingroup upc_marketplace
 */
class MarketplaceElementEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\upc_marketplace\Entity\MarketplaceElementEntity */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Marketplace Element Entity.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Marketplace Element Entity.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.mp_elem_ent.canonical', ['mp_elem_ent' => $entity->id()]);
  }

}
