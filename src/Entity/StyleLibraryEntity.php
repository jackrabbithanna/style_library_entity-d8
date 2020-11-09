<?php

namespace Drupal\style_library_entity\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Style Library entity.
 *
 * @ingroup style_library_entity
 *
 * @ContentEntityType(
 *   id = "style_library_entity",
 *   label = @Translation("Style Library"),
 *   bundle_label = @Translation("Style Library type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\style_library_entity\StyleLibraryEntityListBuilder",
 *     "views_data" = "Drupal\style_library_entity\Entity\StyleLibraryEntityViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\style_library_entity\Form\StyleLibraryEntityForm",
 *       "add" = "Drupal\style_library_entity\Form\StyleLibraryEntityForm",
 *       "edit" = "Drupal\style_library_entity\Form\StyleLibraryEntityForm",
 *       "delete" = "Drupal\style_library_entity\Form\StyleLibraryEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\style_library_entity\StyleLibraryEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\style_library_entity\StyleLibraryEntityAccessControlHandler",
 *   },
 *   base_table = "style_library_entity",
 *   translatable = FALSE,
 *   permission_granularity = "bundle",
 *   admin_permission = "administer style library entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/appearance/style-library-entity/style_library_entity/{style_library_entity}",
 *     "add-page" = "/admin/appearance/style-library-entity/style_library_entity/add",
 *     "add-form" = "/admin/appearance/style-library-entity/style_library_entity/add/{style_library_entity_type}",
 *     "edit-form" = "/admin/appearance/style-library-entity/style_library_entity/{style_library_entity}/edit",
 *     "delete-form" = "/admin/appearance/style-library-entity/style_library_entity/{style_library_entity}/delete",
 *     "collection" = "/admin/appearance/style-library-entity/style_library_entity",
 *   },
 *   bundle_entity_type = "style_library_entity_type",
 *   field_ui_base_route = "entity.style_library_entity_type.edit_form"
 * )
 */
class StyleLibraryEntity extends ContentEntityBase implements StyleLibraryEntityInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Style Library entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Style Library entity.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Style Library is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
