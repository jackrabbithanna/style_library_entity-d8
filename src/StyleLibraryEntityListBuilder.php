<?php

namespace Drupal\style_library_entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class to build a listing of Style Library entities.
 *
 * @ingroup style_library_entity
 */
class StyleLibraryEntityListBuilder extends EntityListBuilder {

  /**
   * Drupal\Core\Form\FormBuilderInterface definition.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Drupal\Core\Render\Renderer definition.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')
        ->getStorage($entity_type->id()),
      $container->get('form_builder'),
      $container->get('renderer')
    );
  }

  /**
   * Constructs a new EntityListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder service.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, FormBuilderInterface $form_builder, Renderer $renderer) {
    parent::__construct($entity_type, $storage);
    $this->formBuilder = $form_builder;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Style Library ID');
    $header['type'] = $this->t('Type');
    $header['name'] = $this->t('Name');
    $header['status'] = $this->t('Enabled');
    $header['global'] = $this->t('Load Globally');
    $header['weight'] = $this->t('Weight');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\style_library_entity\Entity\StyleLibraryEntity $entity */
    $row['id'] = $entity->id();
    $row['type'] = $entity->bundle();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.style_library_entity.edit_form',
      ['style_library_entity' => $entity->id()]
    );
    $status_val = $entity->get('status')->getValue();
    $global_val = $entity->get('global')->getValue();
    $weight_val = $entity->get('weight')->getValue();

    $enable_checkbox = $this->formBuilder->getForm('Drupal\style_library_entity\Form\StyleLibraryEnableCheckboxForm', $row['id']);
    ;
    $load_globally_checkbox = $this->formBuilder->getForm('Drupal\style_library_entity\Form\StyleLibraryLoadGloballyCheckboxForm', $row['id']);

    $row['status'] = $this->renderer->render($enable_checkbox);
    $row['global'] = $this->renderer->render($load_globally_checkbox);
    $row['weight'] = $weight_val[0]['value'];

    return $row + parent::buildRow($entity);
  }

}
