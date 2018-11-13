<?php

namespace Drupal\Tests\cgov_base\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\node\Entity\Node;

/**
 * Tests cgov_base installation profile JavaScript expectations.
 *
 * @group cgov_base
 */
class cgov_baseJavascriptTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'cgov_base';

  /**
   * Tests BigPipe accelerates particular cgov_base installation profile routes.
   */
  public function testBigPipe() {
    $this->drupalLogin($this->drupalCreateUser([
      'access content',
      'post comments',
      'skip comment approval',
    ]));

    $node = Node::create(['type' => 'article'])
      ->setTitle($this->randomMachineName())
      ->setPromoted(TRUE)
      ->setPublished();
    $node->save();

    // Front page: one placeholder, for messages.
    $this->drupalGet('');
    $this->assertBigPipePlaceholderReplacementCount(1);

    // Node page: 3 placeholders:
    // 1. messages
    // 2. local tasks block
    // 3. comment form
    $this->drupalGet($node->toUrl());
    $this->assertBigPipePlaceholderReplacementCount(3);
  }

  /**
   * Asserts the number of BigPipe placeholders that are replaced on the page.
   *
   * @param int $expected_count
   *   The expected number of BigPipe placeholders.
   */
  protected function assertBigPipePlaceholderReplacementCount($expected_count) {
    $web_assert = $this->assertSession();
    $web_assert->waitForElement('css', 'script[data-big-pipe-event="stop"]');
    $page = $this->getSession()->getPage();
    $this->assertCount($expected_count, $this->getDrupalSettings()['bigPipePlaceholderIds']);
    $this->assertCount($expected_count, $page->findAll('css', 'script[data-big-pipe-replacement-for-placeholder-with-id]'));
  }

}