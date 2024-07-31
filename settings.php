<?php

require_once 'config.php';
require_once 'utils.php';
require_once 'settings.php';
require_once 'dbconnection.php';

// Cache array to store values by appId
$cache = [];

/**
 * Get the value based on appId and name parameter.
 *
 * @param mysqli $conn The database connection.
 * @param string $appId The application ID.
 * @param string $name The property name.
 * @return string|null The property value or null if not found.
 */
function getPropertyValue($appId, $name) {
    global $cache;
    
    $conn = getDbConnection();

    // Check if the values for this appId are already cached
    if (!isset($cache[$appId])) {
        // Values are not cached, fetch from database
        $query = "SELECT `name`, `value` FROM `application_property` WHERE `app_id` = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $appId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Initialize an array to store the values
        $values = [];
        while ($row = $result->fetch_assoc()) {
            $values[$row['name']] = $row['value'];
        }

        // Cache the values
        $cache[$appId] = $values;

        // Free result and close statement
        $result->free();
        $stmt->close();
    }

    // Retrieve the value from the cache
    return isset($cache[$appId][$name]) ? $cache[$appId][$name] : null;
}