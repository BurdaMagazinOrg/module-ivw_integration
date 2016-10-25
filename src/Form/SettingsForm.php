<?php

namespace Drupal\ivw_integration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Utility\Token;

/**
 * Defines a form that configures ivw settings.
 */
class SettingsForm extends ConfigFormBase {
  /**
   * The token object.
   *
   * @var Token
   */
  protected $token = array();

  /**
   * Constructs a \Drupal\ivw_integration\SettingsForm object.
   *
   * @param ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param Token $token
   *   The token object.
   */
  public function __construct(ConfigFactoryInterface $config_factory, Token $token) {
    parent::__construct($config_factory);
    $this->token = $token;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('token')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ivw_integration_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $ivw_integration_settings = $this->config('ivw_integration.settings');
    $form['current_code'] = array(
      '#markup' => 'Current Default Code: ' . $this->token->replace($ivw_integration_settings->get('code_template'), array(), array('sanitize' => FALSE)),
    );

    $form['ivw_settings'] = array(
      '#type' => 'vertical_tabs',
      '#default_tab' => 'site_settings',
    );

    $form['site_settings'] = array(
      '#type' => 'details',
      '#title' => $this->t('Site settings'),
      '#open' => TRUE,
      '#group' => 'ivw_settings',
    );

    $form['default_values'] = array(
      '#type' => 'details',
      '#title' => $this->t('Default values'),
      '#open' => FALSE,
      '#group' => 'ivw_settings',
    );

    $form['site_settings']['site'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('IVW Site name'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('site'),
      '#description' => $this->t('Site name as given by IVW, this is used as default for the "st" parameter in the iam_data object'),
    );
    $form['site_settings']['mobile_site'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('IVW Mobile Site name'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('mobile_site'),
      '#description' => $this->t('Mobile site name as given by IVW, this is used as default for the "st" parameter in the iam_data object'),
    );
    $form['site_settings']['code_template'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Code template'),
      '#required' => TRUE,
      '#maxlength' => 256,
      '#size' => 128,
      '#default_value' => $ivw_integration_settings->get('code_template'),
      '#description' => $this->t('Code template, for creating the actual ivw code.'),
    );

    $form['site_settings']['code_template_token_tree'] = array(
      '#theme' => 'token_tree',
      '#token_types' => ['ivw'],
    );

    $form['site_settings']['responsive'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Site is responsive'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('responsive'),
      '#description' => $this->t('Responsive sites must handle mobile code in javascript, this is activated here.'),
    );

    $form['site_settings']['mobile_width'] = array(
      '#type' => 'textfield',
      '#states' => array(
        // Hide the setting when site is not flagged as responsive.
        'invisible' => array(
          ':input[name="ivw_responsive"]' => array('checked' => FALSE),
        ),
      ),
      '#title' => $this->t('Mobile width'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('mobile_width'),
      '#description' => $this->t('On a responsive site, this value tells the javascript up to which screen width, the device should be treated as mobile.'),
    );

    $frabo_default = $ivw_integration_settings->get('frabo_default');
    $form['default_values']['frabo_default'] = array(
      '#type' => 'select',
      '#options' => array(
        'in' => $this->t('in: Deliver questionaire (preferred implementation)'),
        'i2' => $this->t('i2: Alternative implementation, use this if in does not work'),
        'ke' => $this->t('ke: Do not deliver questionaire'),
      ),
      '#title' => $this->t('Frabo control'),
      '#required' => TRUE,
      '#default_value' => $frabo_default ? $frabo_default : 'in',
    );

    $form['default_values']['frabo_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Frabo control is overrideable'),
      '#default_value' => $ivw_integration_settings->get('frabo_overridable'),
    );

    $mobile_frabo_default = $ivw_integration_settings->get('frabo_mobile_default');
    $form['default_values']['frabo_mobile_default'] = array(
      '#type' => 'select',
      '#options' => array(
        'mo' => $this->t('mo: Mobile delivery of questionaire'),
        'ke' => $this->t('ke: Do not deliver questionaire'),
      ),
      '#title' => $this->t('Frabo mobile control'),
      '#required' => TRUE,
      '#default_value' => $mobile_frabo_default ? $mobile_frabo_default : 'mo',
    );

    $form['default_values']['frabo_mobile_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Frabo mobile control is overrideable'),
      '#default_value' => $ivw_integration_settings->get('frabo_mobile_overridable'),
    );

    $form['default_values']['offering_default'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Fallback offering code'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('offering_default'),
      '#description' => $this->t('A single ivw site can have multiple offerings, they can be differentiated by different numbers.'),
      '#min' => 1,
    );

    $form['default_values']['offering_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Offering code is overrideable'),
      '#default_value' => $ivw_integration_settings->get('offering_overridable'),
    );

    $form['default_values']['language_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => $this->t('German'),
        2 => $this->t('Other language, content is verifiable'),
        3 => $this->t('Other language, content is not verifiable'),
      ),
      '#title' => $this->t('Fallback language'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('language_default'),
    );

    $form['default_values']['language_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Language code is overrideable'),
      '#default_value' => $ivw_integration_settings->get('language_overridable'),
    );

    $form['default_values']['format_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => $this->t('Image/Text'),
        2 => $this->t('Audio'),
        3 => $this->t('Video'),
        4 => $this->t('Other dynamic format'),
      ),
      '#title' => $this->t('Fallback format'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('format_default'),
    );

    $form['default_values']['format_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Format code is overrideable'),
      '#default_value' => $ivw_integration_settings->get('format_overridable'),
    );

    $form['default_values']['creator_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => $this->t('Editors'),
        2 => $this->t('User'),
        3 => $this->t('Unknown'),
      ),
      '#title' => $this->t('Fallback creator'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('creator_default'),
    );

    $form['default_values']['creator_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Creator code is overrideable'),
      '#default_value' => $ivw_integration_settings->get('creator_overridable'),
    );

    $form['default_values']['homepage_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => $this->t('Homepage of the site'),
        2 => $this->t('No Homepage'),
        3 => $this->t('Hompage of foreign site'),
      ),
      '#title' => $this->t('Fallback homepage flag'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('homepage_default'),
    );

    $form['default_values']['homepage_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Homepage flag is overridable'),
      '#default_value' => $ivw_integration_settings->get('homepage_overridable'),
    );

    $form['default_values']['delivery_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => $this->t('Online'),
        2 => $this->t('Mobile'),
        3 => $this->t('Connected TV'),
      ),
      '#title' => $this->t('Fallback delivery'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('delivery_default'),
    );

    $form['default_values']['delivery_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Delivery flag is overridable'),
      '#default_value' => $ivw_integration_settings->get('delivery_overridable'),
    );

    $form['default_values']['app_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => $this->t('App'),
        2 => $this->t('No App'),
      ),
      '#title' => $this->t('Fallback app flag'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('app_default'),
    );

    $form['default_values']['app_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('App flag is overridable'),
      '#default_value' => $ivw_integration_settings->get('app_overridable'),
    );

    $form['default_values']['paid_default'] = array(
      '#type' => 'select',
      '#options' => array(
        1 => $this->t('Paid'),
        2 => $this->t('Not assigned'),
      ),
      '#title' => $this->t('Fallback paid flag'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('paid_default'),
    );

    $form['default_values']['paid_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Paid flag is overridable'),
      '#default_value' => $ivw_integration_settings->get('paid_overridable'),
    );

    $form['default_values']['content_default'] = array(
      '#type' => 'select',
      '#options' => array(
        '01' => $this->t('News'),
        '02' => $this->t('Sport'),
        '03' => $this->t('Entertainment/Boulevard/Stars/Film/Music'),
        '04' => $this->t('Fashion/Beauty'),
        '05' => $this->t('Family/Children/Counseling'),
        '06' => $this->t('Life/Psychology/Relationships'),
        '07' => $this->t('Cars/Traffic/Mobility'),
        '08' => $this->t('Travel/Tourism'),
        '09' => $this->t('Computer'),
        '10' => $this->t('Consumer Electronics'),
        '11' => $this->t('Telecommunication/Internet services'),
        '12' => $this->t('Games'),
        '13' => $this->t('Living/Real estate/Garden/Home'),
        '14' => $this->t('Economy/Finance/Job/Career'),
        '15' => $this->t('Health'),
        '16' => $this->t('Food/Beverages'),
        '17' => $this->t('Art/Culture/Litarature'),
        '18' => $this->t('Erotic'),
        '19' => $this->t('Science/Education/Nature/Environment'),
        '20' => $this->t('Information about the offer'),
        '21' => $this->t('Miscellaneous'),
        '22' => $this->t('Remaining topics'),
        '23' => $this->t('Games sitemap'),
        '24' => $this->t('Casual Games'),
        '25' => $this->t('Core Games'),
        '26' => $this->t('Remaining topics (Games)'),
        '27' => $this->t('Social Networking - Private'),
        '28' => $this->t('Social Networking - Business'),
        '29' => $this->t('Dating'),
        '30' => $this->t('Newsletter'),
        '31' => $this->t('E-Mail/SMS/E-Cards'),
        '32' => $this->t('Messenger/Chat'),
        '33' => $this->t('Remaining topics (Networking/Communikation'),
        '34' => $this->t('Searchengine'),
        '35' => $this->t('Directories/Information service'),
        '36' => $this->t('Remaining topics (Searchengine/Directories)'),
        '37' => $this->t('Onlineshops/Shopping Mall/Auctions/B2b Marketplace'),
        '38' => $this->t('Real estate classifieds'),
        '39' => $this->t('Jobs classifieds'),
        '40' => $this->t('Cars classifieds'),
        '41' => $this->t('Miscellaneous classifieds'),
        '42' => $this->t('Miscellaneous (E-Commerce)'),
      ),
      '#title' => $this->t('Fallback content category'),
      '#required' => TRUE,
      '#default_value' => $ivw_integration_settings->get('content_default'),
    );

    $form['default_values']['content_overridable'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Content category is overridable'),
      '#default_value' => $ivw_integration_settings->get('content_overridable'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('ivw_integration.settings')
      ->set('site', $values['site'])
      ->set('mobile_site', $values['mobile_site'])
      ->set('code_template', $values['code_template'])
      ->set('responsive', $values['responsive'])
      ->set('mobile_width', $values['mobile_width'])
      ->set('offering_default', $values['offering_default'])
      ->set('offering_overridable', $values['offering_overridable'])
      ->set('language_default', $values['language_default'])
      ->set('language_overridable', $values['language_overridable'])
      ->set('format_default', $values['format_default'])
      ->set('format_overridable', $values['format_overridable'])
      ->set('creator_default', $values['creator_default'])
      ->set('creator_overridable', $values['creator_overridable'])
      ->set('homepage_default', $values['homepage_default'])
      ->set('homepage_overridable', $values['homepage_overridable'])
      ->set('delivery_default', $values['delivery_default'])
      ->set('delivery_overridable', $values['delivery_overridable'])
      ->set('app_default', $values['app_default'])
      ->set('app_overridable', $values['app_overridable'])
      ->set('paid_default', $values['paid_default'])
      ->set('paid_overridable', $values['paid_overridable'])
      ->set('content_default', $values['content_default'])
      ->set('content_overridable', $values['content_overridable'])
      ->set('frabo_default', $values['frabo_default'])
      ->set('frabo_overridable', $values['frabo_overridable'])
      ->set('frabo_mobile_default', $values['frabo_mobile_default'])
      ->set('frabo_mobile_overridable', $values['frabo_mobile_overridable'])
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ivw_integration.settings',
    ];
  }

}
