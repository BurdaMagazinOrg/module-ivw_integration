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
   *
   * @return string
   */
  public function byCurrentRoute($name);

  /**
   * @param string $name
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *
   * @return string
   */
  public function byRoute($name, RouteMatchInterface $routeMatch);

  /**
   * @param $name
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *
   * @return string
   */
  public function byEntity($name, ContentEntityInterface $entity);

  /**
   * @param string $name
   * @param \Drupal\taxonomy\TermInterface $term
   *
   * @return string
   */
  public function byTerm($name, TermInterface $term);
  
}