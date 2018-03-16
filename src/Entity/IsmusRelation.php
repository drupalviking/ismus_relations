<?php

namespace Drupal\ismus_relations\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the Ismus relation entity.
 *
 * @ingroup ismus_relations
 *
 * @ContentEntityType(
 *   id = "ismus_relation",
 *   label = @Translation("Ismus relation"),
 *   bundle_label = @Translation("Ismus relation type"),
 *   handlers = {
 *     "storage" = "Drupal\ismus_relations\IsmusRelationStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ismus_relations\IsmusRelationListBuilder",
 *     "views_data" = "Drupal\ismus_relations\Entity\IsmusRelationViewsData",
 *     "translation" = "Drupal\ismus_relations\IsmusRelationTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\ismus_relations\Form\IsmusRelationForm",
 *       "add" = "Drupal\ismus_relations\Form\IsmusRelationForm",
 *       "edit" = "Drupal\ismus_relations\Form\IsmusRelationForm",
 *       "delete" = "Drupal\ismus_relations\Form\IsmusRelationDeleteForm",
 *     },
 *     "access" = "Drupal\ismus_relations\IsmusRelationAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\ismus_relations\IsmusRelationHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "ismus_relation",
 *   data_table = "ismus_relation_field_data",
 *   revision_table = "ismus_relation_revision",
 *   revision_data_table = "ismus_relation_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer ismus relation entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/ismus_relation/{ismus_relation}",
 *     "add-page" = "/admin/structure/ismus_relation/add",
 *     "add-form" = "/admin/structure/ismus_relation/add/{ismus_relation_type}",
 *     "edit-form" = "/admin/structure/ismus_relation/{ismus_relation}/edit",
 *     "delete-form" = "/admin/structure/ismus_relation/{ismus_relation}/delete",
 *     "version-history" = "/admin/structure/ismus_relation/{ismus_relation}/revisions",
 *     "revision" = "/admin/structure/ismus_relation/{ismus_relation}/revisions/{ismus_relation_revision}/view",
 *     "revision_revert" = "/admin/structure/ismus_relation/{ismus_relation}/revisions/{ismus_relation_revision}/revert",
 *     "revision_delete" = "/admin/structure/ismus_relation/{ismus_relation}/revisions/{ismus_relation_revision}/delete",
 *     "translation_revert" = "/admin/structure/ismus_relation/{ismus_relation}/revisions/{ismus_relation_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/ismus_relation",
 *   },
 *   bundle_entity_type = "ismus_relation_type",
 *   field_ui_base_route = "entity.ismus_relation_type.edit_form"
 * )
 */
class IsmusRelation extends RevisionableContentEntityBase implements IsmusRelationInterface {

  use EntityChangedTrait;

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
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the ismus_relation owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
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

  public function getRelationA() {
    return $this->get('relation_a')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setRelationA($node_id) {
    $this->set('relation_a', $node_id);
    return $this;
  }

  public function getRelationB() {
    return $this->get('relation_b')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setRelationB($node_id) {
    $this->set('relation_b', $node_id);
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
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['relation_a'] = BaseFieldDefinition::create('entity_reference')
        ->setLabel(t('First part of the relationship'))
        ->setDescription(t('First (A) part of the A is related to B in the relationship.'))
        ->setRevisionable(TRUE)
        ->setSetting('target_type', 'node')
        ->setSetting('handler', 'default')
        ->setTranslatable(TRUE)
        ->setDisplayOptions('view', [
            'label' => 'above',
            'type' => 'string',
            'weight' => -100,
        ])
        ->setDisplayOptions('form', [
            'type' => 'entity_reference_autocomplete',
            'weight' => 5,
            'settings' => [
                'match_operator' => 'CONTAINS',
                'size' => '60',
                'autocomplete_type' => 'tags',
                'placeholder' => '',

            ]
        ])
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);

      $fields['relation_b'] = BaseFieldDefinition::create('entity_reference')
          ->setLabel(t('Second part of the relationship'))
          ->setDescription(t('Second (B) part of the A is related to B in the relationship.'))
          ->setRevisionable(TRUE)
          ->setSetting('target_type', 'node')
          ->setSetting('handler', 'default')
          ->setTranslatable(TRUE)
          ->setDisplayOptions('view', [
              'label' => 'above',
              'type' => 'string',
              'weight' => -100,
          ])
          ->setDisplayOptions('form', [
              'type' => 'entity_reference_autocomplete',
              'weight' => 5,
              'settings' => [
                  'match_operator' => 'CONTAINS',
                  'size' => '60',
                  'autocomplete_type' => 'tags',
                  'placeholder' => '',

              ]
          ])
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Ismus relation entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
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

      /**
       * We really don't want this!
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Ismus relation entity.'))
      ->setRevisionable(TRUE)
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
     */

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Ismus relation is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
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

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
