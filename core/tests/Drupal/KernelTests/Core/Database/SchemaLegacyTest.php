<?php

namespace Drupal\KernelTests\Core\Database;

use Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema;
use Drupal\KernelTests\KernelTestBase;

/**
 * Deprecation tests cases for the schema API.
 *
 * @group legacy
 */
class SchemaLegacyTest extends KernelTestBase {

  /**
   * The modules to enable.
   *
   * @var array
   */
  public static $modules = ['system'];

  /**
   * Tests deprecation of the drupal_get_schema_versions() function.
   *
   * @expectedDeprecation drupal_get_schema_versions() is deprecated in drupal:8.8.0 and is removed from drupal:9.0.0. Use \Drupal\Core\Schema\SchemaDataInterface::getVersions() instead. See https://www.drupal.org/node/2444417
   */
  public function testGetSchemaVersions() {
    $this->assertFalse(drupal_get_schema_versions('system'));
    $module = 'user';
    $this->enableModules([$module]);
    $this->installSchema('user', ['users_data']);
    $expected = $this->container->get('database.schema.data')->getVersions($module);
    $this->assertEquals($expected, drupal_get_schema_versions($module));
  }

  /**
   * Tests deprecation of the drupal_schema_get_field_value() function.
   *
   * @expectedDeprecation drupal_schema_get_field_value() is deprecated in drupal:8.8.0 and is removed from drupal:9.0.0. Use \Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema::castValue($info, $value) instead. See https://www.drupal.org/node/2444417
   */
  public function testSchemaGetFieldValue() {
    $info = ['type' => 'int'];
    $value = 1.1;
    $this->assertEquals(SqlContentEntityStorageSchema::castValue($info, $value), drupal_schema_get_field_value($info, $value));
  }

  /**
   * Tests deprecation of the drupal_get_module_schema() function.
   *
   * @expectedDeprecation drupal_get_module_schema() is deprecated in drupal:8.8.0 and is removed from drupal:9.0.0. Use \Drupal\Core\Schema\SchemaDataInterface::getSpecification() instead. See https://www.drupal.org/node/2444417
   */
  public function testGetModuleSchema() {
    $schema = drupal_get_module_schema('system');
  }

}
