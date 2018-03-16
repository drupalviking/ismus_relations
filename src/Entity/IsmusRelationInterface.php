<?php

namespace Drupal\ismus_relations\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Ismus relation entities.
 *
 * @ingroup ismus_relations
 */
interface IsmusRelationInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Ismus relation creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Ismus relation.
   */
  public function getCreatedTime();

  /**
   * Sets the Ismus relation creation timestamp.
   *
   * @param int $timestamp
   *   The Ismus relation creation timestamp.
   *
   * @return \Drupal\ismus_relations\Entity\IsmusRelationInterface
   *   The called Ismus relation entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Ismus relation published status indicator.
   *
   * Unpublished Ismus relation are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Ismus relation is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Ismus relation.
   *
   * @param bool $published
   *   TRUE to set this Ismus relation to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\ismus_relations\Entity\IsmusRelationInterface
   *   The called Ismus relation entity.
   */
  public function setPublished($published);

  /**
   * Gets the Ismus relation revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Ismus relation revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\ismus_relations\Entity\IsmusRelationInterface
   *   The called Ismus relation entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Ismus relation revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Ismus relation revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\ismus_relations\Entity\IsmusRelationInterface
   *   The called Ismus relation entity.
   */
  public function setRevisionUserId($uid);

  public function getRelationA();

  public function setRelationA($node_id);

  public function getRelationB();

  public function setRelationB($node_id);
}
