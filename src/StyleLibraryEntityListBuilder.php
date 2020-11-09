<?php

namespace Drupal\style_library_entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Style Library entities.
 *
 * @ingroup style_library_entity
 */
class StyleLibraryEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Style Library ID');
    $header['type'] = $this->t('Type');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\style_library_entity\Entity\StyleLibraryEntity $entity */
    $row['id'] = $entity->id();
    $row['type'] = $entity->bundle();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.style_library_entity.edit_form',
      ['style_library_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
