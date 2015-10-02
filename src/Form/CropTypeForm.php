<?php

/**
 * @file
 * Contains \Drupal\crop\Form\CropTypeForm.
 */

namespace Drupal\crop\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form controller for crop type forms.
 */
class CropTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $type = $this->entity;
    $form['#title'] =
      $this->operation == 'add' ?
        $this->t('Add crop type')
        :
        $form['#title'] = $this->t('Edit %label crop type', array('%label' => $type->label()))
    ;

    $form['label'] = [
      '#title' => t('Name'),
      '#type' => 'textfield',
      '#default_value' => $type->label,
      '#description' => t('The human-readable name of this crop type. This name must be unique.'),
      '#required' => TRUE,
      '#size' => 30,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#machine_name' => [
        'exists' => ['\Drupal\crop\Entity\CropType', 'load'],
        'source' => ['label'],
      ],
      '#description' => t('A unique machine-readable name for this crop type. It must only contain lowercase letters, numbers, and underscores.'),
    ];

    $form['description'] = [
      '#title' => t('Description'),
      '#type' => 'textarea',
      '#default_value' => $type->description,
      '#description' => t('Describe this crop type.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = t('Save crop type');
    $actions['delete']['#value'] = t('Delete crop type');
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, FormStateInterface $form_state) {
    parent::validate($form, $form_state);

    $id = trim($form_state->getValue('id'));
    // '0' is invalid, since elsewhere we check it using empty().
    if ($id == '0') {
      $form_state->setErrorByName('type', $this->t("Invalid machine-readable name. Enter a name other than %invalid.", array('%invalid' => $id)));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $type = $this->entity;
    $type->id = trim($type->id());
    $type->label = trim($type->label);

    $status = $type->save();

    $t_args = array('%name' => $type->label());

    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('The crop type %name has been updated.', $t_args));
    } elseif ($status == SAVED_NEW) {
      drupal_set_message(t('The crop type %name has been added.', $t_args));
      $context = array_merge($t_args, array('link' => $this->l(t('View'), new Url('crop.overview_types'))));
      $this->logger('crop')->notice('Added crop type %name.', $context);
    }

    $form_state->setRedirect('crop.overview_types');
  }

}
