<?php

/**
 * @file
 * Definition of Drupal\crop\Tests\CropFunctionalTest.
 */

namespace Drupal\crop\Tests;

use Drupal\Component\Utility\String;
use Drupal\Core\Session\AccountInterface;
use Drupal\simpletest\WebTestBase;

/**
 * Functional tests for crop API.
 *
 * @group crop
 */
class CropFunctionalTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['crop'];

  /**
   * Admin user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser(['administer crop types']);
  }

  /**
   * Tests crop type crud pages.
   */
  public function testCropTypeCRUD() {
    // Anonymous users don't have access to crop type admin pages.
    $this->drupalGet('admin/structure/crop');
    $this->assertResponse(403);
    $this->drupalGet('admin/structure/crop/add');
    $this->assertResponse(403);

    // Can access pages if logged in and no crop types exist.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('admin/structure/crop');
    $this->assertResponse(200);
    $this->assertText(t('No crop types available.'));
    $this->assertLink(t('Add crop type'));

    // Can access add crop type form.
    $this->clickLink(t('Add crop type'));
    $this->assertResponse(200);
    $this->assertUrl('admin/structure/crop/add');

    // Create crop type.
    $edit = [
      'id' => strtolower($this->randomMachineName()),
      'label' => $this->randomString(),
      'description' => $this->randomGenerator->sentences(10),
    ];
    $this->drupalPostForm('admin/structure/crop/add', $edit, t('Save crop type'));
    $this->assertRaw(t('The crop type %name has been added.', ['%name' => $edit['label']]));
    $this->assertUrl('admin/structure/crop');
    $label = $this->xpath("//td[contains(concat(' ',normalize-space(@class),' '),' menu-label ')]");
    $this->assert(strpos($label[0]->asXML(), String::checkPlain($edit['label'])) !== FALSE, 'Crop type label found on listing page.');
    $this->assertText($edit['description']);

    // Check edit form.
    $this->clickLink(t('Edit'));
    $this->assertText(t('Edit @name crop type', ['@name' => $edit['label']]));
    $this->assertRaw($edit['id']);
    $this->assertFieldById('edit-label', $edit['label']);
    $this->assertRaw($edit['description']);

    // Try to access edit form as anonymous user.
    $this->drupalLogout();
    $this->drupalGet('admin/structure/crop/manage/' . $edit['id']);
    $this->assertResponse(403);
    $this->drupalLogin($this->adminUser);

    // Try to create crop type with same machine name.
    $this->drupalPostForm('admin/structure/crop/add', $edit, t('Save crop type'));
    $this->assertText(t('The machine-readable name is already in use. It must be unique.'));

    // Delete crop type.
    $this->drupalGet('admin/structure/crop');
    $this->clickLink(t('Delete'));
    $this->assertText(t('Are you sure you want to delete the crop type @name?', ['@name' => $edit['label']]));
    $this->drupalPostForm('admin/structure/crop/manage/' . $edit['id'] . '/delete', [], t('Delete'));
    $this->assertRaw(t('The crop type %name has been deleted.', ['%name' => $edit['label']]));
    $this->assertText(t('No crop types available.'));
  }

}
