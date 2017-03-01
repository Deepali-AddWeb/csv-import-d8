<?php

namespace Drupal\csv_import\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;
use Drupal\csv_import\CsvImportStorage;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

class EditFieldForm extends FormBase {
  public function getFormID() {
    return 'csv_import.csv_import_add';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
    $parameters = \Drupal::routeMatch()->getParameters();
   /* print('<pre style="color:red;">');
    print_r($parameters->get('field_id'));
    print('</pre>');
    exit;
    get('field_id');*/
    $result = CsvImportStorage::get_field_label('2');
    print('<pre style="color:red;">');
    print_r($result);
    print('</pre>');
    exit;
    $content_type = CsvImportStorage::getcontent_type_name($parameters->get('id'));
    foreach (\Drupal::entityManager()->getFieldDefinitions('node', $content_type) as $field_name => $field_definition) {
      if (!empty($field_definition->getTargetBundle())) {
        $bundleFields[$field_name] = $field_definition->getLabel().' ('.field_name.')';
      }
    } 
    $form['import_id'] = array(
      '#type' => 'hidden',
      '#value' => $parameters->get('id'),
    );
    $form['source'] = array(
      '#type' => 'textfield',
      '#title' => 'Source',
    );
    $form['destination'] = array(
      '#type' => 'select',
      '#title' => 'Content type',
      '#options' => $bundleFields,
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Add'),
    );
    return $form;
  }

  function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('source'))) {
      $form_state->setErrorByName('source', $this->t('Please Enter the Source'));
    }
  }

  function submitForm(array &$form, FormStateInterface $form_state) {
    CsvImportStorage::addimporterfields($form_state->getValue('import_id'), $form_state->getValue('source'), $form_state->getValue('destination'));
    drupal_set_message(t('fields Added successfully'));
    return;
  }

}