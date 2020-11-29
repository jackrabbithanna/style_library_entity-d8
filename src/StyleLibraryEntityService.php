<?php

namespace Drupal\style_library_entity;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\file\Entity\File;
use Drupal\Core\File\FileSystemInterface;
use Drupal\style_library_entity\Entity\StyleLibraryEntity;

class StyleLibraryEntityService {
  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * StyleLibraryEntityService constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, FileSystemInterface $file_system) {
    $this->entityTypeManager = $entity_type_manager;
    $this->fileSystem = $file_system;
  }

  /**
   * @param bool $global_only
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
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
   * @param $fid
   *
   * @return \Drupal\Core\GeneratedUrl|string
   */
  public function getUrlFromFid($fid) {
    $file = File::load($fid);
    if (!empty($file)) {
      $uri = $file->getFileUri();
      return \Drupal\Core\Url::fromUri(file_create_url($uri))->toString();
    }
    return FALSE;
  }

  /**
   * @param \Drupal\style_library_entity\Entity\StyleLibraryEntity $style_library
   *
   * @return array
   */
  public function buildCssFileArrayForEntity(StyleLibraryEntity $style_library) {
    // first the multi-valued drupal field field, css_fid
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

    // add "additional css" field location
    $add_css_uri = 'public://style-library-entity/add-css/style-library-entity-additional-' . $style_library->id() . '.css';
    $add_file_exists = $this->fileSystem->getDestinationFilename($add_css_uri, FileSystemInterface::EXISTS_ERROR );
    if ($add_file_exists === FALSE) {
      $urls[] = \Drupal\Core\Url::fromUri(file_create_url($add_css_uri))->toString();
    }

    return $urls;
  }

  /**
   * @param \Drupal\style_library_entity\Entity\StyleLibraryEntity $style_library
   *
   * @return array
   */
  public function buildJsFileArrayForEntity(StyleLibraryEntity $style_library) {
    // first the multi-valued drupal field field, js_fid
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
   * Deletes the 'additional css' generated file
   *
   * @param $style_library
   */
  public function deleteAdditionalCssFile($style_library) {
    $add_css_uri = 'public://style-library-entity/add-css/style-library-entity-additional-' . $style_library->id() . '.css';
    $this->fileSystem->delete($add_css_uri);
  }
}