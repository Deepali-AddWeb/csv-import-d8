<?php
namespace Drupal\csv_import\Form;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\csv_import\CsvImportStorage;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DeleteProcessor extends ConfirmFormBase {

  public function getFormId() {
    return 'csv_import_importer_delete';
  }

  public function getQuestion() {
    $parameters = \Drupal::routeMatch()->getParameters();
    $processor = CsvImportStorage::get_field_label($parameters->get('field_id'));
    return t('Are you sure you want to delete the Processor for '.$processor['source'].' ?');
  }

  public function getCancelUrl() {
    $parameters = \Drupal::routeMatch()->getParameters();
    return Url::fromRoute('csv_field_processor', array('id' => $parameters->get('id')));
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
    db_delete('csv_import_processor')->condition('field_id', $parameters->get('field_id'))->execute();
    drupal_set_message('Processor Deleted successfully');
    $form_state->setRedirectUrl(Url::fromRoute('csv_field_processor', array('id' => $parameters->get('id'))));
  }

}