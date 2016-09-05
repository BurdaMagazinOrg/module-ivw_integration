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
   * @param boolean $parentOnly
   *    If set to TRUE, skips lookup on first level ivw_settings field
   *
   * @return string
   *    The property value
   */
  public function byCurrentRoute($name, $parentOnly = FALSE);

  /**
   * @param string $name
   *    The name of the IVW property to look up
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *    The route matching the entity (node, term) on which to look up properties
   * @param boolean $parentOnly
   *    If set to TRUE, skips lookup on first level ivw_settings field.
   *    This is used when determining which property the
   *    currently examined entity WOULD inherit if it had
   *    no property for $name in its own ivw settings.
   *
   * @return string
   *    The property value
   */
  public function byRoute($name, RouteMatchInterface $routeMatch, $parentOnly = FALSE);

  /**
   * @param $name
   *    The name of the IVW property to look up
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *    The content entity (usually node) to look up the property on
   * @param boolean $parentOnly
   *    If set to TRUE, skips lookup on first level ivw_settings field.
   *    This is used when determining which property the
   *    currently examined entity WOULD inherit if it had
   *    no property for $name in its own ivw settings.
   *
   * @return string
   *    The property value
   */
  public function byEntity($name, ContentEntityInterface $entity, $parentOnly = FALSE);

  /**
   * @param string $name
   *    The name of the IVW property to look up
   * @param \Drupal\taxonomy\TermInterface $term
   *    The term to look up the property on
   * @param boolean $parentOnly
   *    If set to TRUE, skips lookup on first level ivw_settings field.
   *    This is used when determining which property the
   *    currently examined entity WOULD inherit if it had
   *    no property for $name in its own ivw settings.
   *
   * @return string
   *    The property value
   */
  public function byTerm($name, TermInterface $term, $parentOnly = FALSE);

}
