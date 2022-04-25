<?php

namespace Drupal\style_library_entity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Implements the StyleLibraryLoadGloballyCheckboxForm form controller.
 *
 * Provides form to update Load Globally field in masse.
 */
class StyleLibraryLoadGloballyCheckboxForm extends FormBase {

  /**
   * Drupal\Core\Database\Connection definition.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * StyleLibraryLoadGloballyCheckboxForm constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(Connection $database, EntityTypeManagerInterface $entity_type_manager) {
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'style_library_load_globally_checkbox';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    $query = $this->database->select('style_library_entity', 'sle');
    $query->fields('sle', ['id', 'global']);
    $query->condition('sle.id', $id);
    $query = $query->execute();
    $style_library_entity = $query->fetchAll();

    $global = '';
    foreach ($style_library_entity as $data) {
      $global = $data->global;
    }

    $form['row_id'] = [
      '#type' => 'hidden',
      '#value' => $id,
    ];

    $form['globally_checkbox'] = [
      '#type' => 'checkbox',
      '#title' => 'Enable',
      '#title_display' => 'invisible',
      '#default_value' => $global,
      '#ajax' => [
        'callback' => '::setGloballyCheckbox',
      ],
    ];

    return $form;
  }

  /**
   * Ajax callback for Globally checkbox.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   A AjaxResponse object
   */
  public function setGloballyCheckbox(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $data = $form_state->getValues();

    $this->database->update('style_library_entity')
      ->fields(
          [
            'global' => $data['globally_checkbox'],
          ]
      )
      ->condition('id', $data['row_id'])
      ->execute();

    $this->entityTypeManager->getStorage('style_library_entity')->resetCache([$data['row_id']]);
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
