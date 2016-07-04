<?php

namespace Drupal\ivw_integration;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Path\PathMatcher;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Utility\Token;

/**
 * Class IvwTracker.
 *
 * @package Drupal\ivw_integration
 */
class IvwTracker implements IvwTrackerInterface, CacheableDependencyInterface {
  /**
   * The entity storage object for taxonomy terms.
   *
   * @var TermStorageInterface
   */
  protected $termStorage;

  /**
   * The entity query object for nodes.
   *
   * @var \Drupal\Core\Entity\Query\Sql\Query
   */
  protected $nodeQuery;

  /**
   * The config factory.
   *
   * @var ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The current path match.
   *
   * @var PathMatcher
   */
  protected $pathMatch;

  /**
   * The current route match.
   *
   * @var CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * The token object.
   *
   * @var Token
   */
  protected $token;

  /**
   * Generates IVW tracking information.
   *
   * @param EntityTypeManagerInterface $entity_manager
   *   The entity query object for taxonomy terms.
   * @param QueryFactory $query
   *   The entity query object for taxonomy terms.
   * @param ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param PathMatcher $path_match
   *   The current path match.
   * @param CurrentRouteMatch $current_route_match
   *   The current route match.
   * @param Token $token
   *   Token service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_manager,
    QueryFactory $query,
    ConfigFactoryInterface $config_factory,
    PathMatcher $path_match,
    CurrentRouteMatch $current_route_match,
    Token $token
  ) {
    $this->termStorage = $entity_manager->getStorage('taxonomy_term');
    $this->nodeQuery = $query->get('node');
    $this->configFactory = $config_factory;
    $this->pathMatch = $path_match;
    $this->currentRouteMatch = $current_route_match;
    $this->token = $token;
  }

  /**
   * {@inheritdoc}
   */
  public function getTrackingInformation() {
    return array(
      'st' => $this->getSt(),
      'mobile_st' => $this->getMobileSt(),
      'cp' => $this->getCp(),
      'cpm' => $this->getCpm(),
      'sv' => $this->getSv(),
      'mobile_sv' => $this->getMobileSv(),
    );
  }

  /**
   * Gets the st parameter.
   *
   * @return string
   *   The value of the st parameter.
   */
  protected function getSt() {
    return $this->configFactory->get('ivw_integration.settings')->get('site');
  }

  /**
   * Gets the mobile_st parameter.
   *
   * @return string
   *   The value of the mobile_st parameter.
   */
  protected function getMobileSt() {
    return $this->configFactory->get('ivw_integration.settings')->get('mobile_site');
  }

  /**
   * Gets the cp parameter.
   *
   * Possible overrides have been applied for the current page.
   *
   * @return string
   *   The value of the cp parameter.
   */
  protected function getCp() {
    $settings = $this->configFactory->get('ivw_integration.settings');
    $code_template = $settings->get('code_template');

    return $this->token->replace($code_template, array(), array('sanitize' => FALSE));
  }

  /**
   * Gets the cpm parameter.
   *
   * Possible overrides have been applied for the current page.
   *
   * @return string
   *   The value of the cpm parameter.
   */
  protected function getCpm() {
    // TODO: this is absolutely not generic.
    return str_replace('D1A', 'D2A', $this->getCp());
  }

  /**
   * Gets the sv parameter.
   *
   * @return string
   *   The value of the sv parameter.
   *   If non is defined anywhere 'in' is returned as default.
   */
  protected function getSv() {
    $sv = $this->token->replace('[ivw:frabo]', array(), array('sanitize' => FALSE));
    return empty($sv) ? 'in' : $sv;
  }

  /**
   * Gets the sv parameter for mobile devices.
   *
   * @return string
   *   The value of the sv parameter.
   *   If non is defined anywhere 'mo' is returned as default.
   */
  protected function getMobileSv() {
    $sv = $this->token->replace('[ivw:frabo_mobile]', array(), array('sanitize' => FALSE));
    return empty($sv) ? 'mo' : $sv;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['url.path'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $settings = $this->configFactory->get('ivw_integration.settings');

    return $settings->getCacheTags();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
