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
   * Automatically uses the current route to look up an IVW property.
   *
   * @param string $name
   *    The name of the IVW property to look up
   * @param boolean $termsOnly
   *    If set to TRUE, skips lookup on node settings
   *
   * @return string
   *    The property value
   */
  public function byCurrentRoute($name, $termsOnly = FALSE);

  /**
   * @param string $name
   *    The name of the IVW property to look up
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *    The route matching the entity (node, term) on which to look up properties
   * @param boolean $termsOnly
   *    If set to TRUE, skips lookup on node settings
   *
   * @return string
   *    The property value
   */
  public function byRoute($name, RouteMatchInterface $routeMatch, $termsOnly = FALSE);

  /**
   * @param $name
   *    The name of the IVW property to look up
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *    The content entity (usually node) to look up the property on
   * @param boolean $termsOnly
   *    If set to TRUE, skips lookup on node settings
   *
   * @return string
   *    The property value
   */
  public function byEntity($name, ContentEntityInterface $entity, $termsOnly = FALSE);

  /**
   * @param string $name
   *    The name of the IVW property to look up
   * @param \Drupal\taxonomy\TermInterface $term
   *    The term to look up the property on
   *
   * @return string
   *    The property value
   */
  public function byTerm($name, TermInterface $term);

}
