<?php

namespace Drupal\style_library_entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Style Library edit forms.
 *
 * @ingroup style_library_entity
 */
class StyleLibraryEntityForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\style_library_entity\Entity\StyleLibraryEntity $entity */
    $form = parent::buildForm($form, $form_state);
    $form['extension_type']['widget'][0]['value']['#type'] = 'select';
    $form['extension_type']['widget'][0]['value']['#options'] = [
      '' => 'Not designated',
      'civicrm' => 'CiviCRM',
      'webform' => 'Webform',
      'superfish' => 'Superfish',
    ];
    unset($form['extension_type']['widget'][0]['value']['#size']);
    unset($form['extension_type']['widget'][0]['value']['#maxlength']);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Style Library.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Style Library.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.style_library_entity.canonical', ['style_library_entity' => $entity->id()]);
    $this->createAdditionalCssFile($entity);
  }

  private function createAdditionalCssFile($style_library) {
    $file_system = \Drupal::service('file_system');
    $directory = 'public://style-library-entity/add-css';
    if (!$file_system->prepareDirectory($directory, 1)) {
      \Drupal::logger('style_library_entity')
        ->notice('The data could not be saved because the destination %destination is invalid or not writable.', [
          '%destination' => './web/sites/default/files/style-library-entity/add-css',
        ]);
      \Drupal::messenger()
        ->addError(t('The additional CSS file could not be written.'));
      return FALSE;
    }
    try {
      $additional_css = $style_library->get('add_css')->value;
      $uri = \Drupal::service('file_system')
        ->saveData($additional_css, 'public://style-library-entity/add-css/style-library-entity-additional-' . $style_library->id() . '.css', 1);
      return TRUE;
    }
    catch (\Exception $e) {
      \Drupal::messenger()
        ->addError(t('The additional CSS file could not be written.'));
      return FALSE;
    }
  }
}
