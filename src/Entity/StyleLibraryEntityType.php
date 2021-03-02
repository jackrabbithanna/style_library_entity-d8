<?php

namespace Drupal\style_library_entity\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Style Library type entity.
 *
 * @ConfigEntityType(
 *   id = "style_library_entity_type",
 *   label = @Translation("Style Library type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\style_library_entity\StyleLibraryEntityTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\style_library_entity\Form\StyleLibraryEntityTypeForm",
 *       "edit" = "Drupal\style_library_entity\Form\StyleLibraryEntityTypeForm",
 *       "delete" = "Drupal\style_library_entity\Form\StyleLibraryEntityTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\style_library_entity\StyleLibraryEntityTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "style_library_entity_type",
 *   admin_permission = "administer style library entities",
 *   bundle_of = "style_library_entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/appearance/style-library-entity/types/style_library_entity_type/{style_library_entity_type}",
 *     "add-form" = "/admin/appearance/style-library-entity/types/style_library_entity_type/add",
 *     "edit-form" = "/admin/appearance/style-library-entity/types/style_library_entity_type/{style_library_entity_type}/edit",
 *     "delete-form" = "/admin/appearance/style-library-entity/types/style_library_entity_type/{style_library_entity_type}/delete",
 *     "collection" = "/admin/appearance/style-library-entity/types/style_library_entity_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *   }
 * )
 */
class StyleLibraryEntityType extends ConfigEntityBundleBase implements StyleLibraryEntityTypeInterface {

  /**
   * The Style Library type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Style Library type label.
   *
   * @var string
   */
  protected $label;

}
