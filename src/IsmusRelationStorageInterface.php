<?php

namespace Drupal\ismus_relations;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface IsmusRelationStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Ismus relation revision IDs for a specific Ismus relation.
   *
   * @param \Drupal\ismus_relations\Entity\IsmusRelationInterface $entity
   *   The Ismus relation entity.
   *
   * @return int[]
   *   Ismus relation revision IDs (in ascending order).
   */
  public function revisionIds(IsmusRelationInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Ismus relation author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Ismus relation revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\ismus_relations\Entity\IsmusRelationInterface $entity
   *   The Ismus relation entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(IsmusRelationInterface $entity);

  /**
   * Unsets the language for all Ismus relation with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
