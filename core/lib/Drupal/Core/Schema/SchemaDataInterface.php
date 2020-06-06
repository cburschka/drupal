<?php

namespace Drupal\Core\Schema;

/**
 * Provides an interface for database schema handling.
 */
interface SchemaDataInterface {

  /**
   * Returns an array of available schema versions for a module.
   *
   * @param string $module
   *   A module name.
   *
   * @return array
   *   If the module has updates, an array of available updates sorted by
   *   version. Otherwise, an empty array.
   */
  public function getVersions($module);

  /**
   * Returns the module's schema.
   *
   * This function can be used to retrieve a schema specification in
   * hook_schema(), so it allows you to derive your tables from existing
   * specifications.
   *
   * It is also used by ::install() and ::uninstall() to ensure that a module's
   * tables are created exactly as specified.
   *
   * @param string $module
   *   The module to which the table belongs.
   * @param string $table
   *   (optional) The name of the table. Defaults to NULL, which means that the
   *   module's complete schema is returned.
   *
   * @return array
   *   The schema specification.
   *
   * @see \hook_schema()
   */
  public function getSpecification($module, $table = NULL);

  /**
   * Resets the static cache.
   */
  public function resetCache();

}
