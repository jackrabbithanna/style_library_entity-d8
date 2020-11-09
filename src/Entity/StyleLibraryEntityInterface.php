<?php

namespace Drupal\style_library_entity\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Style Library entities.
 *
 * @ingroup style_library_entity
 */
interface StyleLibraryEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Style Library name.
   *
   * @return string
   *   Name of the Style Library.
   */
  public function getName();

  /**
   * Sets the Style Library name.
   *
   * @param string $name
   *   The Style Library name.
   *
   * @return \Drupal\style_library_entity\Entity\StyleLibraryEntityInterface
   *   The called Style Library entity.
   */
  public function setName($name);

  /**
   * Gets the Style Library creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Style Library.
   */
  public function getCreatedTime();

  /**
   * Sets the Style Library creation timestamp.
   *
   * @param int $timestamp
   *   The Style Library creation timestamp.
   *
   * @return \Drupal\style_library_entity\Entity\StyleLibraryEntityInterface
   *   The called Style Library entity.
   */
  public function setCreatedTime($timestamp);

}
