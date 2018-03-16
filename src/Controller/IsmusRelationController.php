<?php

namespace Drupal\ismus_relations\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\ismus_relations\Entity\IsmusRelationInterface;

/**
 * Class IsmusRelationController.
 *
 *  Returns responses for Ismus relation routes.
 */
class IsmusRelationController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Ismus relation  revision.
   *
   * @param int $ismus_relation_revision
   *   The Ismus relation  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($ismus_relation_revision) {
    $ismus_relation = $this->entityManager()->getStorage('ismus_relation')->loadRevision($ismus_relation_revision);
    $view_builder = $this->entityManager()->getViewBuilder('ismus_relation');

    return $view_builder->view($ismus_relation);
  }

  /**
   * Page title callback for a Ismus relation  revision.
   *
   * @param int $ismus_relation_revision
   *   The Ismus relation  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($ismus_relation_revision) {
    $ismus_relation = $this->entityManager()->getStorage('ismus_relation')->loadRevision($ismus_relation_revision);
    return $this->t('Revision of %title from %date', ['%title' => $ismus_relation->label(), '%date' => format_date($ismus_relation->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Ismus relation .
   *
   * @param \Drupal\ismus_relations\Entity\IsmusRelationInterface $ismus_relation
   *   A Ismus relation  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(IsmusRelationInterface $ismus_relation) {
    $account = $this->currentUser();
    $langcode = $ismus_relation->language()->getId();
    $langname = $ismus_relation->language()->getName();
    $languages = $ismus_relation->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $ismus_relation_storage = $this->entityManager()->getStorage('ismus_relation');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $ismus_relation->label()]) : $this->t('Revisions for %title', ['%title' => $ismus_relation->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all ismus relation revisions") || $account->hasPermission('administer ismus relation entities')));
    $delete_permission = (($account->hasPermission("delete all ismus relation revisions") || $account->hasPermission('administer ismus relation entities')));

    $rows = [];

    $vids = $ismus_relation_storage->revisionIds($ismus_relation);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\ismus_relations\IsmusRelationInterface $revision */
      $revision = $ismus_relation_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $ismus_relation->getRevisionId()) {
          $link = $this->l($date, new Url('entity.ismus_relation.revision', ['ismus_relation' => $ismus_relation->id(), 'ismus_relation_revision' => $vid]));
        }
        else {
          $link = $ismus_relation->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.ismus_relation.translation_revert', ['ismus_relation' => $ismus_relation->id(), 'ismus_relation_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.ismus_relation.revision_revert', ['ismus_relation' => $ismus_relation->id(), 'ismus_relation_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.ismus_relation.revision_delete', ['ismus_relation' => $ismus_relation->id(), 'ismus_relation_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['ismus_relation_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
