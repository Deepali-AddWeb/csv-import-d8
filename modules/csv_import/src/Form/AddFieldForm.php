<?php

/**
 * Adding fields in importer Form
 */
namespace Drupal\csv_import\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;
use Drupal\csv_import\CsvImportStorage;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

class AddFieldForm extends FormBase {

  public function getFormID() {
    return 'csv_import.csv_import_add';
  }


  function buildForm(array $form, FormStateInterface $form_state) {
    $parameters = \Drupal::routeMatch()->getParameters();
    $content_type = CsvImportStorage::getcontent_type_name($parameters->get('id'));
    $content_type = $content_type[0]->content_type;
    $bundleFields['title'] = 'Title';
    foreach (\Drupal::entityManager()->getFieldDefinitions('node', $content_type)
     as $field_name => $field_definition) {
      if (!empty($field_definition->getTargetBundle())) {
        $bundleFields[$field_name] = $field_definition->getLabel() . ' ('.$field_name . ')';
      }
    }
    unset($bundleFields['comment']);
    $form['import_id'] = array(
      '#type' => 'hidden',
      '#value' => $parameters->get('id'),
    );
    $form['source'] = array(
      '#type' => 'machine_name',
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
      '#value' => t('Add Field'),
    );
    return $form;
  }

  // Add field validate form
  function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('source'))) {
      $form_state->setErrorByName('source', $this->t('Please Enter the Source'));
    }
  }

  // Add field submit form
  function submitForm(array &$form, FormStateInterface $form_state) {
    $parameters = \Drupal::routeMatch()->getParameters();

    //submits the form values 
    CsvImportStorage::addimporterfields($form_state->getValue('import_id'),
     $form_state->getValue('source'), $form_state->getValue('destination'));
    
    //sets sucess message
    drupal_set_message(t($form_state->getValue('source') . ' 
      (' . $form_state->getValue('destination') . ') field Added successfully'));
    
    //redirects the page
    $form_state->setRedirectUrl(Url::fromRoute('csv_import_list_fields', array('id' => $parameters->get('id'))));
    return;
  }
}