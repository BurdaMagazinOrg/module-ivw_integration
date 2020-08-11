<?php

namespace Drupal\Tests\ivw_integration\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test override functionality of ivw.
 *
 * @group ivw_integration
 */
class IvwIntegrationOverrideTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'field',
    'node',
    'block',
    'ivw_integration_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * A test user with permission to access the administrative toolbar.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create and log in an administrative user.
    $this->adminUser = $this->drupalCreateUser([
      'administer ivw integration configuration',
    ]);

    $this->config('ivw_integration.theme')->set('site', 'TestSiteName')
      ->set('mobile_site', 'TestMobileSiteName')
      ->set('frabo', 'IN')
      ->set('frabo_mobile', 'mo')
      ->set('frabo_overridable', 0)
      ->set('frabo_mobile_overridable', 0)
      ->set('code_template', '[ivw:offering]L[ivw:language]F[ivw:format]S[ivw:creator]H[ivw:homepage]D[ivw:delivery]A[ivw:app]P[ivw:paid]C[ivw:content]')
      ->set('responsive', 1)
      ->set('mobile_width', 480)
      ->set('offering_default', '01')
      ->set('offering_overridable', 0)
      ->set('language_default', 1)
      ->set('language_overridable', 0)
      ->set('format_default', 1)
      ->set('format_overridable', 0)
      ->set('creator_default', 1)
      ->set('creator_overridable', 0)
      ->set('homepage_default', 2)
      ->set('homepage_overridable', 0)
      ->set('delivery_default', 1)
      ->set('delivery_overridable', 0)
      ->set('app_default', 1)
      ->set('app_overridable', 0)
      ->set('paid_default', 1)
      ->set('paid_overridable', 0)
      ->set('content_default', '01')
      ->set('content_overridable', 1)
      ->set('mcvd', 0)
      ->save();

    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests overriding of site values.
   */
  public function testOverride() {
    // Load the front page.
    $this->drupalGet('node/add/ivw_test');
    $this->assertSession()->statusCodeEquals(200);

    $title = 'ivw test title';
    $format = '2';

    $edit = [
      'title[0][value]' => $title,
      'field_ivw_settings[0][format]' => $format,
    ];

    $this->drupalPostForm(NULL, $edit, 'Save');

    $this->assertSession()->pageTextContains($title);
  }

}
