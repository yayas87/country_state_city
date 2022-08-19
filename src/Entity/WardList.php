<?php

namespace Drupal\country_state_city\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\country_state_city\WardListInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\EntityChangedTrait;

/**
 * Defines the wardlist entity.
 *
 * @ingroup wardlist
 *
 * The following construct is the actual definition of the entity type which
 * is read and cached. Don't forget to clear cache after changes.
 *
 * @ContentEntityType(
 *   id = "wardlist",
 *   label = @Translation("Ward entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\country_state_city\Entity\Controller\WardListBuilder",
 *     "translation" = "Drupal\country_state_city\WardListTranslationHandler",
 *     "form" = {
 *       "default" = "Drupal\country_state_city\Form\WardListForm",
 *       "delete" = "Drupal\country_state_city\Form\WardListDeleteForm",
 *     },
 *     "access" = "Drupal\country_state_city\WardListAccessControlHandler",
 *   },
 *   list_cache_contexts = { "user" },
 *   base_table = "wardlist",
 *   data_table = "wardlist_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer wardlist entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/wardlist/{wardlist}",
 *     "edit-form" = "/wardlist/{wardlist}/edit",
 *     "delete-form" = "/wardlist/{wardlist}/delete",
 *     "collection" = "/wardlist/list"
 *   },
 *   field_ui_base_route = "entity.wardlist.edit_form",
 * )
 *
 * The 'links' above are defined by their path. For core to find the
 * corresponding route, the route name must follow the correct pattern:
 *
 * entity.<entity_type>.<link_name>
 *
 * Example: 'entity.wardlist.canonical'.
 *
 * See the routing file at wardlist.routing.yml for the
 * corresponding implementation.
 */
class WardList extends ContentEntityBase implements WardListInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   *
   * When a new entity instance is added, set the user_id entity reference to
   * the current user as the creator of the instance.
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
  public function getWard() {
    return $this->get('city_id')->entity;
    return $state->getWard();
  }

  /**
   * {@inheritdoc}
   */
  public function getState() {
    return $this->get('state_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountry() {
    $state = $this->get('state_id')->entity;
    return $state->getCountry();
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
   *
   * Define the field properties here.
   *
   * Field name, type and size determine the table structure.
   *
   * In addition, we can define how the field and its content can be
   * manipulated in the GUI. The behaviour of the widgets used can be
   * determined here.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Ward entity.'))
      ->setReadOnly(TRUE);

    $fields['citylist_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('City'))
      ->setDescription(t('The ID of ward of the Ward entity.'))
      ->setSetting('target_type', 'citylist')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
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

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the State entity.'))
      ->setReadOnly(TRUE);

    // Name field for the slider.
    // We set display options for the view as well as the form.
    // Users with correct privileges can change the view and edit
    // configuration.
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the ward entity.'))
      ->setTranslatable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      // Set no default value.
      ->setDefaultValue(NULL)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -6,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -6,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of ward entity.'));
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
