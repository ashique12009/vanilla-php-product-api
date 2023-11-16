<?php 
class User {
    public $email;
    public $password;

    private $connection;
    private $user_table;
    private $project_table;

    public function __construct($db) {
        $this->connection = $db;
        $this->user_table = 'tbl_users';
        $this->project_table = 'tbl_projects';
    }

    public function create_user() {
        $query = "INSERT INTO " . $this->user_table . " SET email=?, password=?";
        $stmt = $this->connection->prepare($query);

        $stmt->bindParam(1, $this->email);
        $stmt->bindParam(2, $this->password);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
