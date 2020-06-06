<?php

/**
 * @file
 * Schema API handling functions.
 */

/**
 * @addtogroup schemaapi
 * @{
 */

/**
 * Indicates that a module has not been installed yet.
 */
const SCHEMA_UNINSTALLED = -1;

/**
 * Returns an array of available schema versions for a module.
 *
 * @param string $module
 *   A module name.
 *
 * @return array|bool
 *   If the module has updates, an array of available updates sorted by
 *   version. Otherwise, FALSE.
 *
 * @deprecated in drupal:8.8.0 and is removed from drupal:9.0.0.
 *   Use \Drupal\Core\Schema\SchemaDataInterface::getVersions() instead.
 *
 * @see https://www.drupal.org/node/2444417
 */
function drupal_get_schema_versions($module) {
  @trigger_error('drupal_get_schema_versions() is deprecated in drupal:8.8.0 and is removed from drupal:9.0.0. Use \Drupal\Core\Schema\SchemaDataInterface::getVersions() instead. See https://www.drupal.org/node/2444417', E_USER_DEPRECATED);
  $versions = \Drupal::service('database.schema.data')->getVersions($module);
  // Service returns an empty array instead of FALSE as before.
  return $versions ?: FALSE;
}

/**
 * Returns the currently installed schema version for a module.
 *
 * @param string $module
 *   A module name.
 * @param bool $reset
 *   Set to TRUE after installing or uninstalling an extension.
 * @param bool $array
 *   Set to TRUE if you want to get information about all modules in the
 *   system.
 *
 * @return string|int
 *   The currently installed schema version, or SCHEMA_UNINSTALLED if the
 *   module is not installed.
 */
function drupal_get_installed_schema_version($module, $reset = FALSE, $array = FALSE) {
  $versions = &drupal_static(__FUNCTION__, []);

  if ($reset) {
    $versions = [];
  }

  if (!$versions) {
    $versions = \Drupal::keyValue('system.schema')->getAll();
    $enabled_modules = \Drupal::moduleHandler()->getModuleList();
    $enabled_modules = array_fill_keys(array_keys($enabled_modules), \Drupal::CORE_MINIMUM_SCHEMA_VERSION);
    $versions = array_merge($enabled_modules, $versions);
  }

  if ($array) {
    return $versions;
  }
  else {
    return isset($versions[$module]) ? $versions[$module] : SCHEMA_UNINSTALLED;
  }
}

/**
 * Updates the installed version information for a module.
 *
 * @param string $module
 *   A module name.
 * @param string $version
 *   The new schema version.
 */
function drupal_set_installed_schema_version($module, $version) {
  \Drupal::keyValue('system.schema')->set($module, $version);
  // Reset the static cache of module schema versions.
  drupal_get_installed_schema_version(NULL, TRUE);
}

/**
 * Creates all tables defined in a module's hook_schema().
 *
 * @param string $module
 *   The module for which the tables will be created.
 */
function drupal_install_schema($module) {
  $schema = \Drupal::service('database.schema.data')->getSpecification($module);
  _drupal_schema_initialize($schema, $module, FALSE);

  foreach ($schema as $name => $table) {
    \Drupal::database()->schema()->createTable($name, $table);
  }
}

/**
 * Removes all tables defined in a module's hook_schema().
 *
 * @param string $module
 *   The module for which the tables will be removed.
 */
function drupal_uninstall_schema($module) {
  $tables = \Drupal::service('database.schema.data')->getSpecification($module);
  _drupal_schema_initialize($tables, $module, FALSE);
  $schema = \Drupal::database()->schema();
  foreach ($tables as $table) {
    if ($schema->tableExists($table['name'])) {
      $schema->dropTable($table['name']);
    }
  }
}

/**
 * Returns a module's schema.
 *
 * This function can be used to retrieve a schema specification in
 * hook_schema(), so it allows you to derive your tables from existing
 * specifications.
 *
 * @param string $module
 *   The module to which the table belongs.
 * @param string $table
 *   The name of the table. If not given, the module's complete schema
 *   is returned.
 *
 * @deprecated in drupal:8.8.0 and is removed from drupal:9.0.0. Use
 *   \Drupal\Core\Schema\SchemaDataInterface::getSpecification() instead.
 *
 * @see https://www.drupal.org/node/2444417
 */
function drupal_get_module_schema($module, $table = NULL) {
  @trigger_error('drupal_get_module_schema() is deprecated in drupal:8.8.0 and is removed from drupal:9.0.0. Use \Drupal\Core\Schema\SchemaDataInterface::getSpecification() instead. See https://www.drupal.org/node/2444417', E_USER_DEPRECATED);
  return \Drupal::service('database.schema.data')->getSpecification($module, $table);
}

/**
 * Fills in required default values for table definitions from hook_schema().
 *
 * @param array $schema
 *   The schema definition array as it was returned by the module's
 *   hook_schema().
 * @param string $module
 *   The module for which hook_schema() was invoked.
 * @param bool $remove_descriptions
 *   (optional) Whether to additionally remove 'description' keys of all tables
 *   and fields to improve performance of serialize() and unserialize().
 *   Defaults to TRUE.
 */
function _drupal_schema_initialize(&$schema, $module, $remove_descriptions = TRUE) {
  // Set the name and module key for all tables.
  foreach ($schema as $name => &$table) {
    if (empty($table['module'])) {
      $table['module'] = $module;
    }
    if (!isset($table['name'])) {
      $table['name'] = $name;
    }
    if ($remove_descriptions) {
      unset($table['description']);
      foreach ($table['fields'] as &$field) {
        unset($field['description']);
      }
    }
  }
}

/**
 * @} End of "addtogroup schemaapi".
 */
