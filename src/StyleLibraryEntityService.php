<?php

namespace Drupal\style_library_entity;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\file\Entity\File;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\style_library_entity\Entity\StyleLibraryEntity;

/**
 * StyleLibraryEntityService, utility functions for managing css/js.
 */
class StyleLibraryEntityService {
  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\File\FileSystemInterface definition.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Drupal\Core\File\FileUrlGeneratorInterface definition.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * StyleLibraryEntityService constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $file_url_generator
   *   The file url generator service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, FileSystemInterface $file_system, FileUrlGeneratorInterface $file_url_generator) {
    $this->entityTypeManager = $entity_type_manager;
    $this->fileSystem = $file_system;
    $this->fileUrlGenerator = $file_url_generator;
  }

  /**
   * Load enabled style library entities.
   *
   * @param bool $enabled
   *   Flag to load only enabled entities or not.
   * @param bool $global_only
   *   Whether to load only style library entities set with global checkbox.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   The loaded style library entities.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function loadEnabledStyleEntities($enabled = TRUE, $global_only = FALSE) {
    $properties = [
      'status' => $enabled ? 1 : 0,
    ];
    if ($global_only) {
      $properties['global'] = 1;
    }
    $style_libraries = $this->entityTypeManager->getStorage('style_library_entity')->loadByProperties($properties);
    return $style_libraries;
  }

  /**
   * Get file url given file id.
   *
   * @param int $fid
   *   The file id.
   *
   * @return \Drupal\Core\GeneratedUrl|string
   *   The file's url.
   */
  public function getUrlFromFid($fid) {
    $file = File::load($fid);
    if (!empty($file)) {
      $uri = $file->getFileUri();
      return $this->fileUrlGenerator->generate($uri)->toString();
    }
    return FALSE;
  }

  /**
   * Builds CSS file array for a style library entity.
   *
   * @param \Drupal\style_library_entity\Entity\StyleLibraryEntity $style_library
   *   A style library entity.
   *
   * @return array
   *   Array of css file urls.
   */
  public function buildCssFileArrayForEntity(StyleLibraryEntity $style_library) {
    // First the multi-valued drupal field field, css_fid.
    $fids = $style_library->get('css_fid');
    $urls = [];
    foreach ($fids->getValue() as $fid_value) {
      if (!empty($fid_value['target_id'])) {
        $url = $this->getUrlFromFid($fid_value['target_id']);
        if (!empty($url)) {
          $urls[] = $url;
        }
      }
    }

    // Add "additional css" field location.
    $add_css_uri = 'public://style-library-entity/add-css/style-library-entity-additional-' . $style_library->id() . '.css';
    $add_file_exists = $this->fileSystem->getDestinationFilename($add_css_uri, FileSystemInterface::EXISTS_ERROR);
    if ($add_file_exists === FALSE) {
      $urls[] = $this->fileUrlGenerator->generate($add_css_uri)->toString();
    }

    return $urls;
  }

  /**
   * Build array of javascript urls for a style library entity.
   *
   * @param \Drupal\style_library_entity\Entity\StyleLibraryEntity $style_library
   *   The style library entity.
   *
   * @return array
   *   Array of urls of javascript files.
   */
  public function buildJsFileArrayForEntity(StyleLibraryEntity $style_library) {
    // First the multi-valued drupal field field, js_fid.
    $fids = $style_library->get('js_fid');
    $urls = [];
    foreach ($fids->getValue() as $fid_value) {
      if (!empty($fid_value['target_id'])) {
        $url = $this->getUrlFromFid($fid_value['target_id']);
        if (!empty($url)) {
          $urls[] = $url;
        }
      }
    }
    return $urls;
  }

  /**
   * Deletes the 'additional css' generated file.
   *
   * @param \Drupal\style_library_entity\Entity\StyleLibraryEntity $style_library
   *   The style library entity.
   */
  public function deleteAdditionalCssFile(StyleLibraryEntity $style_library) {
    $add_css_uri = 'public://style-library-entity/add-css/style-library-entity-additional-' . $style_library->id() . '.css';
    $this->fileSystem->delete($add_css_uri);
  }

}
