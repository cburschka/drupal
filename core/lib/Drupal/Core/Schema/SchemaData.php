<?php

namespace Drupal\Core\Schema;

use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides database schema handling.
 */
class SchemaData implements SchemaDataInterface {

  /**
   * A static cache of schema currentVersions per module.
   *
   * @var array
   */
  protected $allVersions = [];

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a Schema object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function getVersions($module) {
    if (!isset($this->allVersions[$module])) {
      $this->allVersions[$module] = [];

      foreach ($this->moduleHandler->getModuleList() as $loaded_module => $filename) {
        $this->allVersions[$loaded_module] = [];
      }

      // Prepare regular expression to match all possible defined
      // hook_update_N().
      $regexp = '/^(?<module>.+)_update_(?<version>\d+)$/';
      $functions = get_defined_functions();
      // Narrow this down to functions ending with an integer, since all
      // hook_update_N() functions end this way, and there are other
      // possible functions which match '_update_'. We use preg_grep() here
      // instead of foreaching through all defined functions, since the loop
      // through all PHP functions can take significant page execution time
      // and this function is called on every administrative page via
      // system_requirements().
      foreach (preg_grep('/_\d+$/', $functions['user']) as $function) {
        // If this function is a module update function, add it to the list of
        // module updates.
        if (preg_match($regexp, $function, $matches)) {
          $this->allVersions[$matches['module']][] = $matches['version'];
        }
      }
      // Ensure that updates are applied in numerical order.
      foreach ($this->allVersions as &$module_updates) {
        sort($module_updates, SORT_NUMERIC);
      }
      unset($module_updates);
    }

    return empty($this->allVersions[$module]) ? [] : $this->allVersions[$module];
  }

  /**
   * {@inheritdoc}
   */
  public function getSpecification($module, $table = NULL) {
    // Load the .install file to get hook_schema.
    $this->moduleHandler->loadInclude($module, 'install');
    $schema = $this->moduleHandler->invoke($module, 'schema');

    if (isset($table)) {
      if (isset($schema[$table])) {
        return $schema[$table];
      }
    }
    elseif (!empty($schema)) {
      return $schema;
    }

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function resetCache() {
    $this->allVersions = [];
  }

}
