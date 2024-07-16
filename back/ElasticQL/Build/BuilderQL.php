<?php 
namespace ElasticQL\Build;
defined('BASEPATH') OR exit('No direct script access allowed');

function buildQuery($query) {
    global $conn;

    $schema = $query['schema'];
    $fields = implode(", ", $query['fields']);
    $conditions = $query['conditions'];
    $rels = $query['rels'];

    $sql = "SELECT $fields FROM $schema";
    $params = [];

    // Add conditions to the query
    if (!empty($conditions)) {
        $conditionStrings = [];
        foreach ($conditions as $key => $value) {
            $conditionStrings[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        $sql .= " WHERE " . implode(" AND ", $conditionStrings);
    }

    // Prepare and execute the main query
    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Fetch related data
    if (!empty($rels)) {
        foreach ($data as &$row) {
            foreach ($rels as $relSchema => $relDetails) {
                $relFields = implode(", ", $relDetails['fields']);
                $relSql = "SELECT $relFields FROM $relSchema WHERE user_id = :user_id"; // Assuming foreign key is user_id
                $relStmt = $conn->prepare($relSql);
                $relStmt->bindValue(":user_id", $row['id']);
                $relStmt->execute();
                $row[$relSchema] = $relStmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }
    }

    return $data;
}
