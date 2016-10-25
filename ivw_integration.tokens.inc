<?php

/**
 * @file
 * Builds placeholder replacement tokens for ivw-related data.
 */

use Drupal\Component\Utility\Xss;
use Drupal\taxonomy\TermInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Implements hook_token_info().
 */
function ivw_integration_token_info() {
  $type = array(
    'name' => t('IVW'),
    'description' => t('Tokens for IVW data.'),
    'needs-data' => 'ivw',
  );

  // Core comment tokens.
  $ivw['site'] = array(
    'name' => t("IVW site name"),
    'description' => t("Site name as provided by IVW."),
  );
  $ivw['responsive'] = array(
    'name' => t("Responsive flag"),
    'description' => t("Has the value 1 if the site is cionfigured to be responsive, else it is set to 0."),
  );
  $ivw['mobile_width'] = array(
    'name' => t("Mobile width"),
    'description' => t("The maximum width in pixels until a devive is considered to be mobile."),
  );
  $ivw['offering'] = array(
    'name' => t("Offering"),
    'description' => t("An identifier for the offering on this site."),
  );
  $ivw['language'] = array(
    'name' => t("Content language"),
    'description' => t("Content language ID."),
  );
  $ivw['format'] = array(
    'name' => t("Format"),
    'description' => t("The content format ID."),
  );
  $ivw['creator'] = array(
    'name' => t("Creator"),
    'description' => t("The content creator ID."),
  );
  $ivw['homepage'] = array(
    'name' => t('Hompage'),
    'description' => t('Denotes, if the content is the homepage of the offering.'),
  );
  $ivw['delivery'] = array(
    'name' => t("Deliver"),
    'description' => t("Delivery type ID."),
  );
  $ivw['app'] = array(
    'name' => t("App flag"),
    'description' => t("Flags if the offering is an mobile app."),
  );
  $ivw['paid'] = array(
    'name' => t("paid flag"),
    'description' => t("Flags paid content."),
  );
  $ivw['content'] = array(
    'name' => t("Content"),
    'description' => t("Content category identifier."),
  );
  $ivw['frabo'] = array(
    'name' => t("Frabo control"),
    'description' => t("The frabo control variable."),
  );
  $ivw['frabo_mobile'] = array(
    'name' => t("Frabo mobile control"),
    'description' => t("The frabo mobile control variable."),
  );

  return array(
    'types' => array('ivw' => $type),
    'tokens' => array(
      'ivw' => $ivw,
    ),
  );
}

/**
 * Implements hook_tokens().
 */
function ivw_integration_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $sanitize = !empty($options['sanitize']);
  $replacements = array();

  if ($type == 'ivw') {

    $lookupFrom = 'currentRoute';
    if (isset($data['entity']) && $data['entity'] instanceof ContentEntityInterface) {
      $lookupFrom = 'entity';
    }
    elseif (isset($data['term']) && $data['term'] instanceof TermInterface) {
      $lookupFrom = 'term';
    }
    /* @var \Drupal\ivw_integration\IvwLookupServiceInterface $lookup */
    $lookup = \Drupal::service('ivw_integration.lookup');
    foreach ($tokens as $name => $original) {

      switch ($lookupFrom) {

        case 'currentRoute':
          $replacement = $lookup->byCurrentRoute($name);
          break;

        case 'entity':
          $replacement = $lookup->byEntity($name, $data['entity']);
          break;

        case 'term':
          $replacement = $lookup->byTerm($name, $data['term']);
          break;

        default:
          $replacement = NULL;
          break;

      }

      $replacements[$original] = $sanitize ? Xss::filter($replacement) : $replacement;
    }
  }

  return $replacements;
}
