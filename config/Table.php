<?php

abstract class Table {
    private $conn;
    private $table_name;

    private $primary_key;
    private $properties;

    public function __construct($conn, $table_name, $primary_key, $properties) {
        $this->conn = $conn;
        $this->table_name = $table_name;
        $this->primary_key = $primary_key;
        $this->properties = $properties;

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }

    function get() {
        if (!is_null($this->{$this->primary_key})) {
            $query = "SELECT * FROM {$this->table_name} WHERE `{$this->primary_key}` = :{$this->primary_key}";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":{$this->primary_key}", $this->{$this->primary_key});
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data['name'] == null) {
                http_response_code(404);
                echo json_encode(array("message" => "No record found."));
            } else {
                http_response_code(200);
                echo json_encode(array(
                    "records" => $data,
                ));
            }
        } else {
            $query = "SELECT * FROM {$this->table_name}";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                echo json_encode(array("records" => $data));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No record found."));
            }
        }
    }

    function create() {
        if (!is_null($this->{$this->primary_key})) {
            if ($this->has_all_properties()) {
                $query = "INSERT INTO {$this->table_name} SET ";
                foreach ($this->properties as $key) {
                    $query .= "`{$key}` = :{$key}, ";
                }
                $query .= "`{$this->primary_key}` = :{$this->primary_key};";

                $stmt = $this->conn->prepare($query);

                foreach ($this->properties as $key) {
                    $stmt->bindParam(":{$key}", $this->{$key});
                }

                $stmt->bindParam(":{$this->primary_key}", $this->{$this->primary_key});

                if ($stmt->execute()) {
                    http_response_code(201);
                    echo json_encode(array(
                        "message" => "Record created successfully.",
                        "record" => $this->{$this->primary_key},
                    ));
                } else {
                    http_response_code(503);
                    echo json_encode(array(
                        "message" => "Unable to create record.",
                    ));
                }
            } else {
                http_response_code(400);
                echo json_encode(array(
                    "message" => "Unable to create record. Data is incomplete.",
                ));
            }
        } else {
            // disallow method
            http_response_code(405);
            echo json_encode(array("message" => "Method not allowed."));
        }
    }

    function update() {
        if (!is_null($this->{$this->primary_key})) {
            if ($this->has_all_properties()) {
                $query = "UPDATE {$this->table_name} SET ";
                $first = true;
                foreach ($this->properties as $key) {
                    if ($first) {
                        $first = false;
                    } else {
                        $query .= ", ";
                    }
                    $query .= "`{$key}` = :{$key}";
                }
                $query .= " WHERE `{$this->primary_key}` = :{$this->primary_key};";

                $stmt = $this->conn->prepare($query);

                foreach ($this->properties as $key) {
                    $stmt->bindParam(":{$key}", $this->{$key});
                }

                $stmt->bindParam(":{$this->primary_key}", $this->{$this->primary_key});

                if ($stmt->execute()) {
                    http_response_code(200);
                    echo json_encode(array(
                        "message" => "Record updated successfully.",
                        "record" => $this->{$this->primary_key},
                    ));
                } else {
                    http_response_code(503);
                    echo json_encode(array(
                        "message" => "Unable to update record.",
                    ));
                }
            } else {
                http_response_code(400);
                echo json_encode(array(
                    "message" => "Unable to update record. Data is incomplete.",
                ));
            }
        } else {
            // Disallow method
            http_response_code(405);
            echo json_encode(array("message" => "Method not allowed."));
        }
    }

    function delete() {
        if (!is_null($this->{$this->primary_key})) {
            $query = "DELETE FROM {$this->table_name} WHERE `{$this->primary_key}` = :{$this->primary_key};";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":{$this->primary_key}", $this->{$this->primary_key});

            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(array(
                    "message" => "Record deleted successfully.",
                    "record" => $this->{$this->primary_key},
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "message" => "Unable to delete record.",
                ));
            }
        }
    }

    function has_all_properties() {
        foreach ($this->properties as $key) {
            if (!isset($this->{$key})) {
                return false;
            }
        }
        return true;
    }

    function respond() {
        $single = !is_null($this->{$this->primary_key});

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->get();
                break;
            case 'POST':
                $this->create();
                break;
            case 'PUT':
                $this->update();
                break;
            case 'DELETE':
                $this->delete();
                break;
            default:
                http_response_code(405);
                echo json_encode(array(
                    "message" => "Method not allowed.",
                ));
                break;
        }
    }

    function sanitize($string) {
        return htmlspecialchars(strip_tags($string));
    }
}