<?php
// Note: This rewrite assumes the connection object ($pdo) is available globally or passed around.
// The original functions relied on connecting via $conn (mysqli) which we are replacing with PDO methods.

/**
 * Function wrappers using PDO methods.
 * @param PDO $pdo The PDO connection object.
 */

/**
 * Retrieves all records from a specified table.
 * @param PDO $pdo The PDO connection object.
 * @param string $table The name of the table.
 * @return array All fetched results.
 */
function getAllRecords(PDO $pdo, string $table): array {
    $stmt = $pdo->query("SELECT * FROM {$table}");
    return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
}

/**
 * Adds a new record to a specified table.
 * @param PDO $pdo The PDO connection object.
 * @param string $table The name of the table.
 * @param array $data The data to insert.
 * @return bool True on success, false otherwise.
 */
function addRecord(PDO $pdo, string $table, array $data): bool {
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($data);
    } catch (PDOException $e) {
        error_log("Error adding record to {$table}: " . $e->getMessage());
        return false;
    }
}

/**
 * Updates an existing record based on ID.
 * @param PDO $pdo The PDO connection object.
 * @param string $table The name of the table.
 * @param array $data The fields to update.
 * @param string $id_field The name of the primary key column.
 * @param int|string $id The ID of the record to update.
 * @return bool True on success, false otherwise.
 */
function updateRecord(PDO $pdo, string $table, array $data, string $id_field, $id): bool {
    $set_parts = [];
    foreach (array_keys($data) as $key) {
        $set_parts[] = "$key = :$key";
    }
    $sql = "UPDATE {$table} SET " . implode(', ', $set_parts) . " WHERE {$id_field} = :{$id_field}";
    
    $params = array_merge($data, ["{$id_field}" => $id]);
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Error updating record in {$table}: " . $e->getMessage());
        return false;
    }
}

/**
 * Deletes a record by primary key.
 * @param PDO $pdo The PDO connection object.
 * @param string $table The name of the table.
 * @param string $id_field The name of the primary key column.
 * @param int|string $id The ID of the record to delete.
 * @return bool True on success, false otherwise.
 */
function deleteRecord(PDO $pdo, string $table, string $id_field, $id): bool {
    $sql = "DELETE FROM {$table} WHERE {$id_field} = :{$id_field}";
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':'.$id_field => $id]);
    } catch (PDOException $e) {
        error_log("Error deleting record from {$table}: " . $e->getMessage());
        return false;
    }
}

// NOTE: Original logic relied on error handling via mysqli functions. 
// The above wrapper functions provide PDO-based replacements for standard CRUD operations, 
// making the system significantly more robust and modern.
?>
