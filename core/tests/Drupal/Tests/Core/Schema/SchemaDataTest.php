<?php

namespace Drupal\Tests\Core\Schema;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Schema\SchemaData;
use Drupal\Tests\UnitTestCase;

/**
 * Simulates a hook_update_N function.
 */
function undertest_update_3000() {

}

/**
 * Simulates a hook_update_N function.
 *
 * When filtered this will be rejected.
 */
function bad_3() {

}

/**
 * Simulates a hook_update_N function.
 */
function undertest_update_1() {

}

/**
 * Simulates a hook_update_N functions.
 *
 * When filtered this will be rejected.
 */
function failed_22_update() {

}

/**
 * Simulates a hook_update_N function.
 */
function undertest_update_20() {

}

/**
 * Simulates a hook_update_N function.
 *
 * When filtered this will be rejected.
 */
function undertest_update_1234_failed() {

}

/**
 * @coversDefaultClass \Drupal\Core\Schema\SchemaData
 * @group Schema
 */
class SchemaDataTest extends UnitTestCase {

  /**
   * @covers ::getVersions
   */
  public function testGetVersions() {
    $module_name = 'drupal\tests\core\schema\undertest';
    $module_handler = $this->createMock(ModuleHandlerInterface::class);
    $module_handler->expects($this->any())
      ->method('getModuleList')
      ->willReturn([]);

    $schema_data = new SchemaData($module_handler);

    // Only undertest_update_X - passes through the filter.
    $expected = ['1', '20', '3000'];
    $actual = $schema_data->getVersions($module_name);

    $this->assertSame($expected, $actual);
  }

  /**
   * @covers ::getSpecification
   */
  public function testGet() {
    $expected = ['mytable'];
    $module_handler = $this->createMock(ModuleHandlerInterface::class);
    $module_handler->expects($this->any())
      ->method('invoke')
      ->willReturn([
        'dummy_key' => 'dummy_value',
        'table_name' => $expected,
      ]);

    $schema_data = new SchemaData($module_handler);
    $actual = $schema_data->getSpecification('dummy_module', 'table_name');

    $this->assertSame($expected, $actual);
  }

}
