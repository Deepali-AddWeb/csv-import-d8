<?php

/**
 * Add field Processor Form
 */
namespace Drupal\csv_import\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;
use Drupal\csv_import\CsvImportStorage;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AddProcessor extends FormBase {

  public function getFormID() {
    return 'csv_import_add_processor';
  }

  // field processor build form
  function buildForm(array $form, FormStateInterface $form_state) {
    $parameters = \Drupal::routeMatch()->getParameters();
    $result = CsvImportStorage::get_field_label($parameters->get('field_id'));
    
    $form['importer_id'] = array(
      '#type' => 'hidden',
      '#value' => $parameters->get('id'),
    );
    $form['field_id'] = array(
      '#type' => 'hidden',
      '#value' => $parameters->get('field_id'),
    );
    $form['field'] = array(
      '#type' => 'textfield',
      '#title' => t('Field Name'),
      '#default_value' => $result['source'] . ' ('.$result['destination'] . ')',
      '#attributes' => array('readonly' => 'readonly'),
    );
    $form['processor'] = array(
      '#type' => 'textfield',
      '#title' => 'Processor',
      '#description' => 'Add special character for processing multiple
         values in CSV field data. For Example #,@@',
      '#required' => TRUE,
    );
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Add Processor'),
    );
    return $form;
  }

  // field Processor validate form
  function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('processor'))) {
      $form_state->setErrorByName('processor', $this->t('Please Enter the Processor'));
    }
  }

  // field Processor submit form
  function submitForm(array &$form, FormStateInterface $form_state) {
    $parameters = \Drupal::routeMatch()->getParameters();
    CsvImportStorage::addfieldprocessor($form_state->getValue('field_id'),
     $form_state->getValue('processor'), $form_state->getValue('importer_id'));
    drupal_set_message('Field Processor added successfully');
    $form_state->setRedirectUrl(Url::fromRoute('csv_field_processor', 
      array('id' => $parameters->get('id'))));
  }
}