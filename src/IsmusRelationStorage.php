<?php

namespace Drupal\ismus_relations;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\ismus_relations\Entity\IsmusRelationInterface;

/**
 * Defines the storage handler class for Ismus relation entities.
 *
 * This extends the base storage class, adding required special handling for
 * Ismus relation entities.
 *
 * @ingroup ismus_relations
 */
class IsmusRelationStorage extends SqlContentEntityStorage implements IsmusRelationStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(IsmusRelationInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {ismus_relation_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {ismus_relation_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(IsmusRelationInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {ismus_relation_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('ismus_relation_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
