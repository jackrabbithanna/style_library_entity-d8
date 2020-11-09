<?php

namespace Drupal\style_library_entity\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Style Library entities.
 */
class StyleLibraryEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
