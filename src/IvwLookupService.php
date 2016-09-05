<?php

namespace Drupal\ivw_integration;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\TermInterface;
use Drupal\taxonomy\TermStorage;

class IvwLookupService implements IvwLookupServiceInterface {

  const supportedEntityParameters = ['node', 'media', 'taxonomy_term'];
  /**
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $currentRouteMatch;
  /**
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;
  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(RouteMatchInterface $currentRouteMatch, ConfigFactoryInterface $configFactory, EntityTypeManagerInterface $entityTypeManager) {
    $this->currentRouteMatch = $currentRouteMatch;
    $this->config            = $configFactory->get('ivw_integration.settings');
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * @inheritdoc
   */
  public function byCurrentRoute($name, $parentOnly = FALSE) {
    return $this->byRoute($name, $this->currentRouteMatch, $parentOnly);
  }

  /**
   * @inheritdoc
   */
  public function byRoute($name, RouteMatchInterface $route, $parentOnly = FALSE) {

    $entity = NULL;

    foreach (static::supportedEntityParameters as $parameter) {
      /**
       * @var ContentEntityInterface $entity
       */
      if ($entity = $route->getParameter($parameter)) {

        // FIXME is this part required?
        if (is_numeric($entity)) {
          $entity = Node::load($entity);
        }
        if ($entity instanceof TermInterface) {
          $setting = $this->searchTerm($name, $entity, $parentOnly);
        } else {
          $setting = $this->searchEntity($name, $entity, $parentOnly);
        }

        if ($setting !== NULL) {
          return $setting;
        }
      }
    }

    return $this->defaults($name);
  }

  /**
   * @inheritdoc
   */
  public function byEntity($name, ContentEntityInterface $entity, $parentOnly = FALSE) {
    $result = $this->searchEntity($name, $entity, $parentOnly);
    return $result !== NULL ? $result : $this->defaults($name);
  }

  /**
   * @inheritdoc
   */
  public function byTerm($name, TermInterface $term, $parentOnly = FALSE) {
    $result = $this->searchTerm($name, $term, $parentOnly);
    return $result !== NULL ? $result : $this->defaults($name);
  }

  /**
   * @param string $name
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   * @param boolean $parentOnly
   *
   * @return string|NULL
   */
  protected function searchEntity($name, ContentEntityInterface $entity, $parentOnly = FALSE) {
    //  Search for ivw_integration_settings field
    foreach ($entity->getFieldDefinitions() as $fieldDefinition) {
      $fieldType = $fieldDefinition->getType();
      if (!$parentOnly) {
        /*
         * If settings are found, check if an overridden value for the
         * given setting is found and return that
         */
        $overiddenSetting = $this->getOverriddenIvwSetting($name, $fieldDefinition, $entity);
        if (isset($overiddenSetting)) {
          return $overiddenSetting;
        }
      }

      // Check for fallback categories if no ivw_integration_setting is found
      if (!isset($termOverride) && $fieldType === 'entity_reference' && $fieldDefinition->getSetting('target_type') === 'taxonomy_term') {
        $fieldName = $fieldDefinition->getName();
        if ($tid = $entity->$fieldName->target_id) {
          $term = Term::load($tid);
          if ($term) {
            $termOverride = $this->searchTerm($name, $term);
          }
        }
      }
    }

    // If we did not return before, it is possible that we found a termOverride
    if (isset($termOverride)) {
      return $termOverride;
    }

    return NULL;
  }

  /**
   * @param string $name
   * @param \Drupal\taxonomy\TermInterface $term
   *
   * @param bool $parentOnly
   * @return null|string
   */
  protected function searchTerm($name, TermInterface $term, $parentOnly = FALSE) {
    if (!$parentOnly) {
      foreach ($term->getFieldDefinitions() as $fieldDefinition) {
        $override = $this->getOverriddenIvwSetting($name, $fieldDefinition, $term);
        if (isset($override)) {
          return $override;
        }
      }
    }

    /**
     * @var TermStorage $termStorage
     */
    $termStorage = $this->entityTypeManager->getStorage('taxonomy_term');

    foreach ($termStorage->loadParents($term->id()) as $parent) {
      $override = $this->searchTerm($name, $parent);
      if (isset($override)) {
        return $override;
      }
    }

    return NULL;
  }

  /**
   * @param string $name
   * @param \Drupal\Core\Field\FieldDefinitionInterface $fieldDefinition
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *
   * @return string|null
   */
  protected function getOverriddenIvwSetting($name, FieldDefinitionInterface $fieldDefinition, ContentEntityInterface $entity) {
    if ($fieldDefinition->getType() === 'ivw_integration_settings') {
      $fieldName = $fieldDefinition->getName();
      if (!empty($entity->$fieldName->get(0)->$name)) {
        return $entity->$fieldName->get(0)->$name;
      }
    }
    return NULL;
  }

  /**
   * @param string $name
   *
   * @return string|NULL
   */
  private function defaults($name) {
    return $this->config->get($name . '_default');
  }

}
