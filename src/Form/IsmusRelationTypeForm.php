<?php

namespace Drupal\ismus_relations\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class IsmusRelationTypeForm.
 */
class IsmusRelationTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $ismus_relation_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $ismus_relation_type->label(),
      '#description' => $this->t("Label for the Ismus relation type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $ismus_relation_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ismus_relations\Entity\IsmusRelationType::load',
      ],
      '#disabled' => !$ismus_relation_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $ismus_relation_type = $this->entity;
    $status = $ismus_relation_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Ismus relation type.', [
          '%label' => $ismus_relation_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Ismus relation type.', [
          '%label' => $ismus_relation_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($ismus_relation_type->toUrl('collection'));
  }

}
