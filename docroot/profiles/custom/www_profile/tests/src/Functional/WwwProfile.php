<?php

namespace Drupal\Tests\www_profile_domain\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests www_profile_domain installation profile expectations.
 *
 * @group www_profile_domain
 */
class www_profile_domainTest extends BrowserTestBase {

  protected $profile = 'www_profile_domain';

  /**
   * Tests www_profile_domain installation profile.
   */
  public function testwww_profile_domain() {
    $this->drupalGet('');
    // Check the login block is present.
    $this->assertLink(t('Create new account'));
    $this->assertResponse(200);

    // Create a user to test tools and navigation blocks for logged in users
    // with appropriate permissions.
    $user = $this->drupalCreateUser(['access administration pages', 'administer content types']);
    $this->drupalLogin($user);
    $this->drupalGet('');
    $this->assertText(t('Tools'));
    $this->assertText(t('Administration'));

    // Ensure that there are no pending updates after installation.
    $this->drupalLogin($this->rootUser);
    $this->drupalGet('update.php/selection');
    $this->assertText('No pending updates.');

    // Ensure that there are no pending entity updates after installation.
    $this->assertFalse($this->container->get('entity.definition_update_manager')->needsUpdates(), 'After installation, entity schema is up to date.');

    // Ensure special configuration overrides are correct.
    $this->assertFalse($this->config('system.theme.global')->get('features.node_user_picture'), 'Configuration system.theme.global:features.node_user_picture is FALSE.');
    $this->assertEquals(USER_REGISTER_VISITORS_ADMINISTRATIVE_APPROVAL, $this->config('user.settings')->get('register'));
  }

}