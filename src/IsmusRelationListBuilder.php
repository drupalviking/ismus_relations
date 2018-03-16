<?php

namespace Drupal\ismus_relations;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Ismus relation entities.
 *
 * @ingroup ismus_relations
 */
class IsmusRelationListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Ismus relation ID');
    $header['name'] = $this->t('Relation');
    $header['relation_name'] = $this->t('Relation name');
    $header['relation_machine_name'] = $this->t('Relation machine name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\ismus_relations\Entity\IsmusRelation */
    $relation_a = \Drupal::entityTypeManager()->getStorage('node')->load($entity->getRelationA());
    $relation_b = \Drupal::entityTypeManager()->getStorage('node')->load($entity->getRelationB());
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $relation_a->getTitle() . ' -> ' . $relation_b->getTitle(),
      'entity.ismus_relation.edit_form',
      ['ismus_relation' => $entity->id()]
    );

    $bundle = \Drupal::entityTypeManager()->getStorage('ismus_relation_type')->load($entity->bundle());
    $row['relation_name'] = $bundle->label();
    $row['relation_machine_name'] = $bundle->getOriginalId();
    return $row + parent::buildRow($entity);
  }
}
