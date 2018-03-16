<?php

namespace Drupal\ismus_relations\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Ismus relation type entity.
 *
 * @ConfigEntityType(
 *   id = "ismus_relation_type",
 *   label = @Translation("Ismus relation type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ismus_relations\IsmusRelationTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ismus_relations\Form\IsmusRelationTypeForm",
 *       "edit" = "Drupal\ismus_relations\Form\IsmusRelationTypeForm",
 *       "delete" = "Drupal\ismus_relations\Form\IsmusRelationTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ismus_relations\IsmusRelationTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "ismus_relation_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "ismus_relation",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/ismus_relation_type/{ismus_relation_type}",
 *     "add-form" = "/admin/structure/ismus_relation_type/add",
 *     "edit-form" = "/admin/structure/ismus_relation_type/{ismus_relation_type}/edit",
 *     "delete-form" = "/admin/structure/ismus_relation_type/{ismus_relation_type}/delete",
 *     "collection" = "/admin/structure/ismus_relation_type"
 *   }
 * )
 */
class IsmusRelationType extends ConfigEntityBundleBase implements IsmusRelationTypeInterface {

  /**
   * The Ismus relation type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Ismus relation type label.
   *
   * @var string
   */
  protected $label;

}
