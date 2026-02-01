import fs from 'fs/promises';
import path from 'path';
import { pathToFileURL } from 'url';

/**
 * Recursively scans all units in the Modules/Units directory
 * and collects Vite configurations from Common directories
 *
 * @param {string} basePath - Base path to start scanning from
 * @param {string} currentPath - Current path being scanned
 * @param {Array} collectedPaths - Array to collect all found paths
 * @returns {Promise<Array>} - Array of all collected asset paths
 */
async function scanUnitsRecursively(basePath, currentPath, collectedPaths = []) {
  try {
    const fullPath = path.join(basePath, currentPath);
    const items = await fs.readdir(fullPath);

    for (const item of items) {
      // Skip hidden files and system files
      if (item.startsWith('.') || item === '.DS_Store') {
        continue;
      }

      const itemPath = path.join(currentPath, item);
      const fullItemPath = path.join(basePath, itemPath);

      try {
        const stats = await fs.stat(fullItemPath);

        if (stats.isDirectory()) {
          // Check if this directory has a Common subdirectory with vite.config.js
          const commonPath = path.join(fullItemPath, 'Common');

          const viteConfigPath = path.join(commonPath, 'vite.config.js');

          try {
            // Check if Common directory and vite.config.js exist
            await fs.access(viteConfigPath);
            // Import the unit's Vite configuration
            const moduleConfigURL = pathToFileURL(viteConfigPath);
            const moduleConfig = await import(moduleConfigURL.href);

            if (moduleConfig.paths && Array.isArray(moduleConfig.paths)) {
              // Add only the specific paths from this unit's configuration
              // Filter out any glob patterns and keep only specific file paths
              const specificPaths = moduleConfig.paths.filter(path =>
                !path.includes('*') &&
                !path.includes('**') &&
                (path.endsWith('.js') || path.endsWith('.css') || path.endsWith('.scss') || path.endsWith('.sass'))
              );

              if (specificPaths.length > 0) {
                collectedPaths.push(...specificPaths);
              }
            }

          } catch (error) {
            // vite.config.js doesn't exist in Common directory, continue scanning
          }

          // Recursively scan child units
          await scanUnitsRecursively(basePath, itemPath, collectedPaths);
        }
      } catch (error) {
        // Skip items that can't be accessed
        continue;
      }
    }
  } catch (error) {
    console.error(`Error scanning directory ${currentPath}: ${error}`);
  }

  return collectedPaths;
}

/**
 * Recursively scans for Filament directories and collects all PHP files
 *
 * @param {string} basePath - Base path to start scanning from
 * @param {string} currentPath - Current path being scanned
 * @param {Array} collectedPaths - Array to collect all found PHP file paths
 * @returns {Promise<Array>} - Array of all collected PHP file paths
 */
async function scanFilamentDirectories(basePath, currentPath, collectedPaths = []) {
  try {
    const fullPath = path.join(basePath, currentPath);
    const items = await fs.readdir(fullPath);

    for (const item of items) {
      // Skip hidden files and system files
      if (item.startsWith('.') || item === '.DS_Store') {
        continue;
      }

      const itemPath = path.join(currentPath, item);
      const fullItemPath = path.join(basePath, itemPath);

      try {
        const stats = await fs.stat(fullItemPath);

        if (stats.isDirectory()) {
          // Check if this directory is named "Filament"
          if (item === 'Filament') {

            // Found a Filament directory, scan it deeply for PHP files
            await scanFilamentForPhpFiles(fullItemPath, itemPath, collectedPaths);
          } else {
            // Continue scanning deeper for more Filament directories
            await scanFilamentDirectories(basePath, itemPath, collectedPaths);
          }
        }
      } catch (error) {
        // Skip items that can't be accessed
        continue;
      }
    }
  } catch (error) {
    console.error(`Error scanning directory ${currentPath}: ${error}`);
  }

  return collectedPaths;
}

/**
 * Recursively scans a Filament directory and its subdirectories for PHP files
 *
 * @param {string} fullFilamentPath - Full path to the Filament directory
 * @param {string} relativeFilamentPath - Relative path to the Filament directory
 * @param {Array} collectedPaths - Array to collect all found PHP file paths
 * @returns {Promise<Array>} - Array of all collected PHP file paths
 */
async function scanFilamentForPhpFiles(fullFilamentPath, relativeFilamentPath, collectedPaths = []) {
  try {
    const items = await fs.readdir(fullFilamentPath);

    for (const item of items) {
      // Skip hidden files and system files
      if (item.startsWith('.') || item === '.DS_Store') {
        continue;
      }

      const itemFullPath = path.join(fullFilamentPath, item);
      const itemRelativePath = path.join(relativeFilamentPath, item);

      try {
        const stats = await fs.stat(itemFullPath);

        if (stats.isDirectory()) {
          // Recursively scan subdirectories
          await scanFilamentForPhpFiles(itemFullPath, itemRelativePath, collectedPaths);
        } else if (stats.isFile() && item.endsWith('.php')) {
          // Found a PHP file, add it to the collection
          collectedPaths.push(itemRelativePath);

        }
      } catch (error) {
        // Skip items that can't be accessed
        continue;
      }
    }
  } catch (error) {
    console.error(`Error scanning Filament directory ${fullFilamentPath}: ${error}`);
  }

  return collectedPaths;
}

/**
 * Recursively scans for Blade template files (.blade.php) in a given directory
 *
 * @param {string} basePath - Base path to start scanning from
 * @param {string} currentPath - Current path being scanned
 * @param {Array} collectedPaths - Array to collect all found Blade file paths
 * @returns {Promise<Array>} - Array of all collected Blade file paths
 */
async function scanForBladeFiles(basePath, currentPath, collectedPaths = []) {
  try {
    const fullPath = path.join(basePath, currentPath);
    const items = await fs.readdir(fullPath);

    for (const item of items) {
      // Skip hidden files and system files
      if (item.startsWith('.') || item === '.DS_Store') {
        continue;
      }

      const itemPath = path.join(currentPath, item);
      const fullItemPath = path.join(basePath, itemPath);

      try {
        const stats = await fs.stat(fullItemPath);

        if (stats.isDirectory()) {
          // Recursively scan subdirectories
          await scanForBladeFiles(basePath, itemPath, collectedPaths);
        } else if (stats.isFile() && item.endsWith('.blade.php')) {
          // Found a Blade template file, add it to the collection
          collectedPaths.push(itemPath);
        }
      } catch (error) {
        // Skip items that can't be accessed
        continue;
      }
    }
  } catch (error) {
    console.error(`Error scanning directory ${currentPath} for Blade files: ${error}`);
  }

  return collectedPaths;
}

/**
 * Specifically scans for Admin, My, Manage directories and their Common subdirectories
 *
 * @param {string} basePath - Base path to start scanning from
 * @param {Array} collectedPaths - Array to collect all found paths
 * @returns {Promise<Array>} - Array of all collected asset paths
 */
async function scanSpecificDirectories(basePath, collectedPaths = []) {
  const unitsPath = path.join(basePath, 'Units');

  try {
    await fs.access(unitsPath);
    const unitItems = await fs.readdir(unitsPath);

    for (const unitItem of unitItems) {
      if (unitItem.startsWith('.') || unitItem === '.DS_Store') {
        continue;
      }

      const unitPath = path.join(unitsPath, unitItem);
      const unitStats = await fs.stat(unitPath);
      if (unitStats.isDirectory()) {
        // Scan for Admin, My, Manage directories in this unit
        const subItems = await fs.readdir(unitPath);

        for (const subItem of subItems) {
          if (subItem.startsWith('.') || subItem === '.DS_Store') {
            continue;
          }

          // Check if this is Admin, My, or Manage directory
          if (['Admin', 'My', 'Manage'].includes(subItem)) {
            const subPath = path.join(unitPath, subItem);
            const subStats = await fs.stat(subPath);

            if (subStats.isDirectory()) {
              // Check for Common directory with vite.config.js

              const viteConfigPath = path.join(subPath, 'vite.config.js');

              try {

                  await fs.access(viteConfigPath);
                // Import the unit's Vite configuration
                const moduleConfigURL = pathToFileURL(viteConfigPath);
                const moduleConfig = await import(moduleConfigURL.href);

                if (moduleConfig.paths && Array.isArray(moduleConfig.paths)) {
                  // Add only the specific paths from this unit's configuration
                  const specificPaths = moduleConfig.paths.filter(path =>
                    !path.includes('*') &&
                    !path.includes('**') &&
                    (path.endsWith('.js') || path.endsWith('.css') || path.endsWith('.scss') || path.endsWith('.sass'))
                  );

                  if (specificPaths.length > 0) {
                    collectedPaths.push(...specificPaths);
                  }
                }

              } catch (error) {
                // vite.config.js doesn't exist in directory, skip
              }

              // Also scan deeper for nested Admin, My, Manage directories
              await scanSpecificDirectoriesRecursively(subPath, collectedPaths);
            }
          }
        }
      }
    }
  } catch (error) {
    console.error(`Error scanning specific directories: ${error}`);
  }

  return collectedPaths;
}

/**
 * Recursively scans for nested Admin, My, Manage directories
 *
 * @param {string} currentPath - Current path being scanned
 * @param {Array} collectedPaths - Array to collect all found paths
 * @returns {Promise<Array>} - Array of all collected asset paths
 */
async function scanSpecificDirectoriesRecursively(currentPath, collectedPaths = []) {
  try {
    const items = await fs.readdir(currentPath);

    for (const item of items) {
      if (item.startsWith('.') || item === '.DS_Store') {
        continue;
      }

      const itemPath = path.join(currentPath, item);
      const stats = await fs.stat(itemPath);

      if (stats.isDirectory()) {
        // Check if this is Admin, My, or Manage directory
        if (['Admin', 'My', 'Manage'].includes(item)) {
          const commonPath = path.join(itemPath, 'Common');
          const viteConfigPath = path.join(commonPath, 'vite.config.js');

          try {
            await fs.access(viteConfigPath);

            // Import the unit's Vite configuration
            const moduleConfigURL = pathToFileURL(viteConfigPath);
            const moduleConfig = await import(moduleConfigURL.href);

            if (moduleConfig.paths && Array.isArray(moduleConfig.paths)) {
              // Add only the specific paths from this unit's configuration
              const specificPaths = moduleConfig.paths.filter(path =>
                !path.includes('*') &&
                !path.includes('**') &&
                (path.endsWith('.js') || path.endsWith('.css') || path.endsWith('.scss') || path.endsWith('.sass'))
              );

              if (specificPaths.length > 0) {
                collectedPaths.push(...specificPaths);
              }
            }

          } catch (error) {
            // vite.config.js doesn't exist in Common directory, skip
          }
        }

        // Continue scanning deeper
        await scanSpecificDirectoriesRecursively(itemPath, collectedPaths);
      }
    }
  } catch (error) {
    console.error(`Error scanning specific directories recursively: ${error}`);
  }

  return collectedPaths;
}

/**
 * Main function to collect all module unit asset paths
 * All units are activated (no module status checking)
 *
 * @param {Array} paths - Initial paths array
 * @param {string} modulesPath - Path to Modules directory
 * @returns {Promise<Array>} - Array of all collected asset paths
 */
async function collectModuleAssetsPaths(paths, modulesPath) {
  const unitsPath = path.join(modulesPath, 'Units');

  try {
    // Check if Units directory exists
    await fs.access(unitsPath);

    // Start recursive scanning from the Units directory
    const collectedPaths = await scanUnitsRecursively(modulesPath, 'Units');

    // Also scan specifically for Admin, My, Manage directories
    const specificPaths = await scanSpecificDirectories(modulesPath, []);

    // Scan for Blade template files in resources directory (relative to project root)
    const projectRoot = path.dirname(modulesPath); // Go up one level from Modules to project root
    const resourcesPath = path.join(projectRoot, 'resources');

    try {
      await fs.access(resourcesPath);
    } catch (error) {
      console.log('Resources directory not found, skipping Blade file scan');
    }

    // Combine all paths and remove duplicates
    const allPaths = [...collectedPaths, ...specificPaths];

    const uniquePaths = [...new Set(allPaths)];

    if (uniquePaths.length > 0) {
      paths.push(...uniquePaths);
    }

  } catch (error) {
    console.error(`Error accessing Units directory: ${error}`);
  }

  return paths;
}

export default collectModuleAssetsPaths;
