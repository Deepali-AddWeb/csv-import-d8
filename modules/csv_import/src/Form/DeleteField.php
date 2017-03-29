<?php

/**
 * Delete field confirmation Form
 */
namespace Drupal\csv_import\Form;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\csv_import\CsvImportStorage;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DeleteField extends ConfirmFormBase {

  public function getFormId() {
    return 'csv_import_field_delete';
  }

  public function getQuestion() {
    $parameters = \Drupal::routeMatch()->getParameters();
    $content_type = CsvImportStorage::get_field_label($parameters->get('field_id'));
    return t('Are you sure you want to delete the ' . $content_type['source'].' ?');
  }

  public function getCancelUrl() {
    $parameters = \Drupal::routeMatch()->getParameters();
    return Url::fromRoute('csv_import_list_fields', 
      array('id' => $parameters->get('id')));
  }

  public function getDescription() {
    return t('This action cannot be undone.');
  }
  
  public function getConfirmText() {
    return t('Delete');
  }

  public function getCancelText() {
    return t('Cancel');
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $parameters = \Drupal::routeMatch()->getParameters();
    CsvImportStorage::deletefields($parameters->get('field_id'));
    drupal_set_message('Processor Deleted successfully');
    $form_state->setRedirectUrl(Url::fromRoute('csv_import_list_fields', 
      array('id' => $parameters->get('id'))));
  }

}