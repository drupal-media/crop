<?php

/**
 * @file
 * Contains \Drupal\crop\Tests\CropCRUDTest.
 */

namespace Drupal\crop\Tests;

use Drupal\Component\Utility\String;
use Drupal\simpletest\KernelTestBase;

/**
 * Tests the crop entity CRUD operations.
 *
 * @group crop
 */
class CropCRUDTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('user', 'image', 'crop');

  /**
   * The crop storage.
   *
   * @var \Drupal\crop\CropStorageInterface.
   */
  protected $controller;

  /**
   * Test image style.
   *
   * @var \Drupal\image\ImageStyleInterface
   */
  protected $testStyle;

  protected function setUp() {
    parent::setUp();

    /** @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager */
    $entity_manager = $this->container->get('entity.manager');
    $this->controller = $entity_manager->getStorage('crop');

    // Create DB schemas.
    $entity_manager->onEntityTypeCreate($entity_manager->getDefinition('user'));
    $entity_manager->onEntityTypeCreate($entity_manager->getDefinition('image_style'));
    $entity_manager->onEntityTypeCreate($entity_manager->getDefinition('crop'));

    // Create test image style
    $this->testStyle = $entity_manager->getStorage('image_style')->create([
      'name' => 'test',
      'label' => 'Test image style',
      'effects' => [],
    ]);
    $this->testStyle->save();
  }

  /**
   * Tests save.
   */
  public function testCropSave() {
    /** @var \Drupal\crop\CropInterface $crop */
    $values = [
      'entity_id' => 1,
      'entity_type' => 'file',
      'x' => '100',
      'y' => '150',
      'width' => '200',
      'height' => '250',
      'image_style' => $this->testStyle->id(),
    ];
    $crop = $this->controller->create($values);

    try {
      $crop->save();
      $this->assertTrue(TRUE, 'Crop saved correctly.');
    } catch (\Exception $exception) {
      $this->assertTrue(FALSE, 'Crop not saved correctly.');
    }

    $loaded_crop = $this->controller->loadUnchanged(1);
    foreach ($values as $key => $value) {
      switch ($key) {
        case 'image_style':
          $this->assertEqual($loaded_crop->{$key}->target_id, $value, String::format('Correct value for @field found.', ['@field' => $key]));
          break;

        default:
          $this->assertEqual($loaded_crop->{$key}->value, $value, String::format('Correct value for @field found.', ['@field' => $key]));
          break;
      }
    }

  }
}
