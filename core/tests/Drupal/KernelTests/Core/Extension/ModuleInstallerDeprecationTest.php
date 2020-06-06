<?php

namespace Drupal\KernelTests\Core\Extension;

use Drupal\Core\DrupalKernelInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ModuleInstaller;
use Drupal\KernelTests\KernelTestBase;

/**
 * @group legacy
 * @group extension
 * @coversDefaultClass \Drupal\Core\Extension\ModuleInstaller
 */
class ModuleInstallerDeprecationTest extends KernelTestBase {

  /**
   * @covers ::__construct
   * @expectedDeprecation Calling ModuleInstaller::__construct() without the $schema_data argument is deprecated in drupal:8.8.0. The $schema_data argument will be required in drupal:9.0.0.. See https://www.drupal.org/project/drupal/issues/2124069.
   */
  public function testConstructorDeprecation() {
    $root = '';
    $module_handler = $this->prophesize(ModuleHandlerInterface::class);
    $kernel = $this->prophesize(DrupalKernelInterface::class);
    $this->assertNotNull(new ModuleInstaller($root, $module_handler->reveal(), $kernel->reveal()));
  }

}