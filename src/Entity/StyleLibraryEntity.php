<?php

namespace Drupal\style_library_entity\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use \Drupal\Component\Utility\Environment;
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
        'weight' => 10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 10,
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
        'weight' => -10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Style Library is enabled.'))
      ->setLabel(t('Enabled'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -9,
      ]);

    /* Additional Fields */
    // Load globally?
    // Style Entity Library for D8 generates libraries that can be included via render arrays specifically, or globally?
    $fields['global'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Load Globally'))
      ->setDescription(t('Load for every page load, any theme. Do not check this if style library loaded specifically by theme or other modules for specific types of pages or components.'))
      ->setDefaultValue(TRUE)
      ->setSettings(['on_label' => 'Global', 'off_label' => 'Render Array'])
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'boolean',
        'weight' => -8,
      ])
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -8,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // drupal library weights
    $fields['weight'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Weight'))
      ->setDescription(t('Order Style Libraries are loaded in.'))
      ->setDefaultValue(0)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'integer',
        'weight' => -6,
      ])
      ->setDisplayOptions('form', [
        'type' => 'integer_textfield',
        'weight' => -6,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    // Extension Type
    $fields['extension_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Extension Type'))
      ->setDescription(t('Extension type. Used by other modules / themes to further categorize style libraries'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -7,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -7,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Additional CSS
    $fields['add_css'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Additional CSS'))
      ->setDescription(t('Enter CSS to be loaded when this style library is used. Will take precedence over files uploaded below.'))
      ->setDefaultValue('')
      ->setRequired(FALSE)
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'basic_string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => -4,
        'settings' => ['rows' => 4],
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);


    // CSS Files
    $validators = array(
      'file_validate_extensions' => array('css'),
      'file_validate_size' => array(Environment::getUploadMaxSize()),
    );
    $fields['css_fid'] = BaseFieldDefinition::create('file')
      ->setLabel(t('CSS Files'))
      ->setCardinality(-1)
      ->setRequired(FALSE)
      ->setDescription(t('Upload cascading style sheet (CSS) files.'))
      ->setSetting('upload_validators', $validators)
      ->setSetting('file_extensions', 'css')
      ->setSetting('upload_location', 'public://style-library-entity/css')
      ->setSetting('file_directory', 'style-library-entity/css')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'file',
        'weight' => -3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'file',
        'settings' => array(
          'upload_validators' => $validators,
          'upload_location' => 'public://style-library-entity/css'
        ),
        'weight' => -3,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);


    // JS files
    $validators = array(
      'file_validate_extensions' => array('js'),
      'file_validate_size' => array(Environment::getUploadMaxSize()),
    );

    $fields['js_fid'] = BaseFieldDefinition::create('file')
      ->setLabel(t('JS Files'))
      ->setCardinality(-1)
      ->setRequired(FALSE)
      ->setDescription(t('Upload javascript (JS) files.'))
      ->setSetting('upload_validators', $validators)
      ->setSetting('file_extensions', 'js')
      ->setSetting('upload_location', 'public://style-library-entity/js')
      ->setSetting('file_directory', 'style-library-entity/js')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'file',
        'weight' => -2,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'file',
        'settings' => array(
          'upload_validators' => $validators,
          'upload_location' => 'public://style-library-entity/js'
        ),
        'weight' => -2,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);


    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));


    return $fields;
  }

}
