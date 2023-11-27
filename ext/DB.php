<?php /** @noinspection SqlNoDataSourceInspection */

namespace ext;

use Exception;
use mysqli;

class DB
{
    private string $DB_HOST = 'schule.winnert1.dbs.hostpoint.internal';
    private string $DB_USER = 'winnert1_schule';
    private string $DB_PASSWORD = 'FEA9PNz3p+tu+8!?MPrP';
    private string $DB_NAME = 'winnert1_m295raul';
    private mysqli $conn;
    private string $table;
    private string $column;
    private string $id;

    public function __construct($table, $column, $id) {
        header('Content-Type: application/json');

        try {
            $this->conn = new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASSWORD, $this->DB_NAME);

            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            if (!$this->conn->set_charset('utf8')) {
                throw new Exception("Error setting charset: " . $this->conn->error);
            }

            $this->table = $table ?? '';
            $this->column = $column ?? '';
            $this->id = $id ?? '';

            $this->start();
        } catch (Exception $e) {
            $this->handleError($e->getMessage());
        }
    }

    private function start(): void {
        try {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $this->insertData();
                    break;

                case 'PUT':
                case 'PATCH':
                    $this->updateData();
                    break;

                case 'DELETE':
                    $this->deleteData();
                    break;

                case 'GET':
                    $this->getData();
                    break;

                default:
                    http_response_code(400);
            }
        } catch (Exception $e) {
            $this->handleError($e->getMessage());
        }
    }

    private function getData(): void {
        try {
            if ($this->id && $this->column) {
                $query = 'SELECT * FROM tbl_' . $this->table . ' WHERE ' . $this->column . ' = ' . $this->id . ';';
            } else {
                $query = 'SELECT * FROM tbl_' . $this->table . ';';
            }

            $result = $this->conn->query($query);

            if ($result === false) {
                throw new Exception('Error in query: ' . $this->conn->error);
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            if (empty($data)) {
                http_response_code(204);
            } else {
                http_response_code(200);
                echo json_encode(['data' => $data]);
            }
        } catch (Exception $e) {
            $this->handleError($e->getMessage());
        }
    }



    private function deleteData(): void {
        try {
            if (empty($this->id) && empty($this->column)) {
                $query = 'DELETE FROM tbl_' . $this->table . ';';
            } else {
                $query = 'DELETE FROM tbl_' . $this->table;

                if (!empty($this->column)) {
                    $query .= ' WHERE ' . $this->column . ' = ' . $this->id;
                }

                $query .= ';';
            }

            $result = $this->conn->query($query);

            if ($result === false) {
                throw new Exception('Error deleting record: ' . $this->conn->error);
            }

            echo json_encode(['message' => 'Record deleted successfully']);
        } catch (Exception $e) {
            $this->handleError($e->getMessage());
        }
    }

    
private function updateData(): void {
        try {
            // Get the request body
            $requestData = json_decode(file_get_contents('php://input'), true);
    
            // Check if request body is valid JSON
            if ($requestData === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON data in request body');
            }
    
            // Ensure the required data is present
            if (empty($requestData)) {
                throw new Exception('No data provided for update');
            }

            $requestData = Sanitize::sanitizeRequest($requestData);
    
            // Construct the SET part of the SQL query
            $setValues = [];
            foreach ($requestData as $key => $value) {
                $key = $this->conn->real_escape_string($key);
                $value = $this->conn->real_escape_string($value);
                $setValues[] = "$key = '$value'";
            }
    
            $setValuesString = implode(', ', $setValues);
    
            // Construct the SQL query
            $whereClause = (!empty($this->id) && !empty($this->column)) ? "WHERE $this->column = $this->id" : '';
            $query = "UPDATE tbl_$this->table SET $setValuesString $whereClause;";
    
            // Execute the query
            $result = $this->conn->query($query);
    
            // Check if the query was successful
            if ($result === false) {
                throw new Exception('Error updating record: ' . $this->conn->error);
            }
    
            // Return a success message
            echo json_encode(['message' => 'Record updated successfully']);
        } catch (Exception $e) {
            $this->handleError($e->getMessage());
        }
    }


    private function insertData(): void {
        try {
            // Get the request body
            $requestData = json_decode(file_get_contents('php://input'), true);
    
            
            // Check if request body is valid JSON
            if ($requestData === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON data in request body');
            }
    
            // Ensure the required data is present
            if (empty($requestData)) {
                throw new Exception('No data provided for insert');
            }

            $requestData = Sanitize::sanitizeRequest($requestData);
    
            // Escape column names and values
            $columns = array_map([$this->conn, 'real_escape_string'], array_keys($requestData));
            $values = array_map([$this->conn, 'real_escape_string'], array_values($requestData));
    
            // Construct the SQL query
            $columnsString = implode(', ', $columns);
            $valuesString = implode("', '", $values);
    
            $query = "INSERT INTO tbl_$this->table ($columnsString) VALUES ('$valuesString');";
    
            // Execute the query
            $result = $this->conn->query($query);
    
            // Check if the query was successful
            if ($result === false) {
                throw new Exception('Error inserting record: ' . $this->conn->error);
            }
    
            // Return a success message
            echo json_encode(['message' => 'Record inserted successfully']);
        } catch (Exception $e) {
            $this->handleError($e->getMessage());
        }
    }

    private function handleError($message): void {
        http_response_code(500);
        echo json_encode(['error' => $message]);
    }
}
