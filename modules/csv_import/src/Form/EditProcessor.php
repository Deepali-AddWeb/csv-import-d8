<?php

namespace Drupal\csv_import\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;
use Drupal\csv_import\CsvImportStorage;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

class EditProcessor extends FormBase {
  public function getFormID() {
    return 'csv_import_edit_processor';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
    $parameters = \Drupal::routeMatch()->getParameters();
    $result = CsvImportStorage::get_field_label($parameters->get('field_id'));
    $value = CsvImportStorage::getprocessor($parameters->get('field_id'));
   
    $form['field_id'] = array(
      '#type' => 'hidden',
      '#value' => $parameters->get('field_id'),
    );
    $form['field'] = array(
      '#type' => 'textfield',
      '#title' => t('Field Name'),
      '#default_value' => $result['source'].' ('.$result['destination'].')',
      '#attributes' => array('readonly' => 'readonly'),
    );
    $form['processor'] = array(
      '#type' => 'textfield',
      '#title' => 'Processor',
      '#default_value' => $value[0]->processor,
      '#description' => 'Add special character for processing multiple values in CSV field data. For Example #,@@',
      '#required' => TRUE,
    );
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Update Processor'),
    );
    return $form;
  }

  function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('processor'))) {
      $form_state->setErrorByName('processor', $this->t('Please Enter the Processor'));
    }
  }

  function submitForm(array &$form, FormStateInterface $form_state) {
    CsvImportStorage::updatefieldprocessor($form_state->getValue('field_id'), $form_state->getValue('processor'));
    drupal_set_message('Processor Updated successfully');
    return;
  }
}