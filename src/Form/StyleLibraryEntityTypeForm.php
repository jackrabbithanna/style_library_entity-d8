<?php

namespace Drupal\style_library_entity\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for creating/updating style library entity types.
 */
class StyleLibraryEntityTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $style_library_entity_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $style_library_entity_type->label(),
      '#description' => $this->t("Label for the Style Library type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $style_library_entity_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\style_library_entity\Entity\StyleLibraryEntityType::load',
      ],
      '#disabled' => !$style_library_entity_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $style_library_entity_type = $this->entity;
    $status = $style_library_entity_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Style Library type.', [
          '%label' => $style_library_entity_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Style Library type.', [
          '%label' => $style_library_entity_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($style_library_entity_type->toUrl('collection'));
  }

}
