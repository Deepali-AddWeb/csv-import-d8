<?php

/**
 * Delete Importer confirmation Form
 */
namespace Drupal\csv_import\Form;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\csv_import\CsvImportStorage;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DeleteImporter extends ConfirmFormBase {

  public function getFormId() {
    return 'csv_import_importer_delete';
  }

  public function getQuestion() {
    $parameters = \Drupal::routeMatch()->getParameters();
    $content_type = CsvImportStorage::getcontent_type_name($parameters->get('id'));
    return t('Are you sure you want to delete the ' . $content_type[0]->name . ' ?');
  }

  public function getCancelUrl() {
    $parameters = \Drupal::routeMatch()->getParameters();
    return Url::fromRoute('csv_import_root');
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
    db_delete('csv_import')->condition('id', $parameters->get('id'))->execute();
    db_delete('csv_import_fields')->condition('importer_id', 
      $parameters->get('id'))->execute();
    db_delete('csv_import_processor')->condition('importer_id', 
      $parameters->get('id'))->execute();
    drupal_set_message('Importer Deleted successfully');
    $form_state->setRedirectUrl(Url::fromRoute('csv_import_root'));
  }

}