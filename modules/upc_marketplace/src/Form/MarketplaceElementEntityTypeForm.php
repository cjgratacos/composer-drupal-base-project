<?php

namespace Drupal\upc_marketplace\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MarketplaceElementEntityTypeForm.
 */
class MarketplaceElementEntityTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $mp_elem_ent_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => 255,
      '#default_value' => $mp_elem_ent_type->label(),
      '#description' => $this->t("Title for the Marketplace Element Entity type."),
      '#required' => TRUE,
    ];

    $form['description'] = [
      '#title' => t('Description'),
      '#type' => 'textarea',
      '#default_value' => $mp_elem_ent_type->getDescription(),
      '#description' => t('This text will be displayed on the <em>Add New Marketplace Element</em> page.'),
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $mp_elem_ent_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\upc_marketplace\Entity\MarketplaceElementEntityType::load',
      ],
      '#disabled' => !$mp_elem_ent_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $mp_elem_ent_type = $this->entity;
    $status = $mp_elem_ent_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Marketplace Element Entity type.', [
          '%label' => $mp_elem_ent_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Marketplace Element Entity type.', [
          '%label' => $mp_elem_ent_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($mp_elem_ent_type->toUrl('collection'));
  }

}
