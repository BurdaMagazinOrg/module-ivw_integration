<?php

/**
 * Copyright © 2016 Valiton GmbH.
 */

namespace Drupal\ivw_integration;


use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\taxonomy\TermInterface;

interface IvwLookupServiceInterface {

  /**
   * @param string $name
   * @param boolean $parentsOnly
   *
   * @return string
   */
  public function byCurrentRoute($name, $parentsOnly);

  /**
   * @param string $name
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   * @param boolean $parentsOnly
   *
   * @return string
   */
  public function byRoute($name, RouteMatchInterface $routeMatch, $parentsOnly);

  /**
   * @param $name
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   * @param boolean $parentsOnly
   *
   * @return string
   */
  public function byEntity($name, ContentEntityInterface $entity, $parentsOnly);

  /**
   * @param string $name
   * @param \Drupal\taxonomy\TermInterface $term
   *
   * @return string
   */
  public function byTerm($name, TermInterface $term);

}
