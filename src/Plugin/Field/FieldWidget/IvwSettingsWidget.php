<?php

namespace Drupal\ivw_integration\Plugin\Field\FieldWidget;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\ivw_integration\IvwLookupServiceInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'ivw_integration_settings' widget.
 *
 * @FieldWidget(
 *   id = "ivw_integration_widget",
 *   module = "ivw_integration",
 *   label = @Translation("IVW Settings"),
 *   field_types = {
 *     "ivw_integration_settings"
 *   }
 * )
 */
class IvwSettingsWidget extends WidgetBase implements ContainerFactoryPluginInterface {
  /**
   * The config factory.
   *
   * @var ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * @var \Drupal\ivw_integration\IvwLookupServiceInterface
   */
  protected $lookupService;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    ConfigFactoryInterface $config_factory,
    IvwLookupServiceInterface $lookup_service
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);

    $this->configFactory = $config_factory;
    $this->lookupService = $lookup_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('config.factory'),
      $container->get('ivw_integration.lookup')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(
    FieldItemListInterface $items,
    $delta,
    array $element,
    array &$form,
    FormStateInterface $form_state
  ) {
    $settings = $this->configFactory->get('ivw_integration.settings');
    $empty_option = 'Parent value (:value)';

    if ($settings->get('offering_overridable')) {
      $element['offering'] = array(
        '#type' => 'textfield',
        '#title' => t('Offering code'),
        '#default_value' => isset($items[$delta]->offering) ? $items[$delta]->offering : NULL,
        '#description' => t('A single ivw site can have multiple offerings, they can be differentiated by different numbers.'),
        '#required' => FALSE,
        '#empty_option' => t('Parent value'),
        '#min' => 1,
      );
    }

    if ($settings->get('language_overridable')) {
      $element['language'] = array(
        '#type' => 'select',
        '#options' => array(
          1 => 'Deutsch',
          2 => 'Andere Sprache, Inhalt prüfbar',
          3 => 'Andere Sprache, Inhalt nicht prüfbar',
        ),
        '#title' => t('Language'),
        '#required' => FALSE,
        '#empty_option' => t('Parent value'),
        '#default_value' => isset($items[$delta]->language) ? $items[$delta]->language : NULL,
      );
    }

    if ($settings->get('format_overridable')) {
      $element['format'] = array(
        '#type' => 'select',
        '#options' => array(
          1 => 'Bild/Text',
          2 => 'Audio',
          3 => 'Video',
          4 => 'Andere dynamische Formate',
        ),
        '#title' => t('Format'),
        '#required' => FALSE,
        '#empty_option' => t('Parent value'),
        '#default_value' => isset($items[$delta]->format) ? $items[$delta]->format : NULL,
      );
    }

    if ($settings->get('creator_overridable')) {
      $element['creator'] = array(
        '#type' => 'select',
        '#options' => array(
          1 => 'Redaktion',
          2 => 'User',
          3 => 'Unbekannt',
        ),
        '#title' => t('Creator'),
        '#required' => FALSE,
        '#empty_option' => t('Parent value'),
        '#default_value' => isset($items[$delta]->creator) ? $items[$delta]->creator : NULL,
      );
    }

    if ($settings->get('homepage_overridable')) {
      $element['homepage'] = array(
        '#type' => 'select',
        '#options' => array(
          1 => 'Homepage des Angebots',
          2 => 'Keine Homepage',
          3 => 'Hompage der Fremddomains bei Multi-Angeboten',
        ),
        '#title' => t('Homepage flag'),
        '#required' => FALSE,
        '#empty_option' => t('Parent value'),
        '#default_value' => isset($items[$delta]->homepage) ? $items[$delta]->homepage : NULL,
      );
    }

    if ($settings->get('delivery_overridable')) {
      $element['delivery'] = array(
        '#type' => 'select',
        '#options' => array(
          1 => 'Online',
          2 => 'Mobile',
          3 => 'Connected TV',
        ),
        '#title' => t('Delivery'),
        '#required' => FALSE,
        '#empty_option' => t('Parent value'),
        '#default_value' => isset($items[$delta]->delivery) ? $items[$delta]->delivery : NULL,
      );
    }

    if ($settings->get('app_overridable')) {
      $element['app'] = array(
        '#type' => 'select',
        '#options' => array(
          1 => 'App',
          2 => 'Keine App',
        ),
        '#title' => t('Fallback app flag'),
        '#required' => FALSE,
        '#empty_option' => t('Parent value'),
        '#default_value' => isset($items[$delta]->app) ? $items[$delta]->app : NULL,
      );
    }

    if ($settings->get('paid_overridable')) {
      $element['paid'] = array(
        '#type' => 'select',
        '#options' => array(
          1 => 'Paid',
          2 => 'Nicht zugeordnet',
        ),
        '#title' => t('Paid flag'),
        '#required' => FALSE,
        '#empty_option' => t('Parent value'),
        '#default_value' => isset($items[$delta]->paid) ? $items[$delta]->paid : NULL,
      );
    }

    if ($settings->get('content_overridable')) {
      $name = 'content';
      $options = array(
        '01' => 'Nachrichten',
        '02' => 'Sport',
        '03' => 'Entertainment/Boulevard/Stars/Film/Musik',
        '04' => 'Fashion/Beauty',
        '05' => 'Familie/Kinder/Lebenshilfe',
        '06' => 'Liebe/Psychologie/Beziehungen',
        '07' => 'Fahrzeuge/Verkehr/Mobilität',
        '08' => 'Reise/Touristik',
        '09' => 'Computer',
        '10' => 'Consumer Electronics',
        '11' => 'Telekommunikation/Internetdienste',
        '12' => 'Spiele',
        '13' => 'Wohnen/Immobilien/Garten/Haushalt',
        '14' => 'Wirtschaft/Finanzen/Job/Karriere',
        '15' => 'Gesundheit',
        '16' => 'Essen/Trinken',
        '17' => 'Kunst/Kultur/Literatur',
        '18' => 'Erotik',
        '19' => 'Wissenschaft/Bildung/Natur/Umwelt',
        '20' => 'Angebotsinformation',
        '21' => 'Vermischtes (multithematisch)',
        '22' => 'Sonstiges (monothematisch)',
        '23' => 'Übersichtsseite zu Spiele',
        '24' => 'Casual Games',
        '25' => 'Core Games',
        '26' => 'Sonstiges (Bereich Spiele)',
        '27' => 'Social Networking - Privat',
        '28' => 'Social Networking - Business',
        '29' => 'Partnersuche/Dating',
        '30' => 'Newsletter',
        '31' => 'E-Mail/SMS/E-Cards',
        '32' => 'Messenger/Chat',
        '33' => 'Sonstiges (Bereich Networking/Kommunikation',
        '34' => 'Suchmaschinen',
        '35' => 'Verzeichnisse/Auskunftsdienste',
        '36' => 'Sonstiges (Bereich Suchmaschinen/Verzeichnisse)',
        '37' => 'Onlineshops/Shopping Mall/Auktionen/B2bMarktplätze',
        '38' => 'Immobilien Rubrikenmärkte/Kleinanzeigen',
        '39' => 'Jobs Rubrikenmärkte/Kleinanzeigen',
        '40' => 'Fahrzeuge Rubrikenmärkte/Kleinanzeigen',
        '41' => 'Sonstiges Rubrikenmärkte/Kleinanzeigen',
        '42' => 'Sonstiges (Bereich E-Commerce)',
      );
      $element[$name] = array(
        '#type' => 'select',
        '#group' => 'ivw_integration_settings_override',
        '#options' => $options,
        '#title' => t('Content category'),
        '#required' => FALSE,
        '#empty_option' => t($empty_option, array(':value' => $options[$this->getParentSetting($name)])),
        '#default_value' => isset($items[$delta]->$name) ? $items[$delta]->$name : NULL,
      );
    }

    return $element;
  }

  // TODO see whether this works (only difference here would be the override lookup)
  private function getParentSetting($name) {
    return $this->lookupService->byCurrentRoute($name, TRUE);
  }

}
