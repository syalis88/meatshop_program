<?php
require_once "meatshopDB.php";

class Meat extends Database {

    public $id;
    public $name;
    public $category;
    public $price;
    public $image;

    public function viewMeat($search = "", $category = "") {
        $conn = $this->connect();

        $sql = "SELECT m.id, m.name, m.price, m.image, c.category_name
                FROM meat AS m
                JOIN categories AS c ON m.category_id = c.category_id
                WHERE 1";

        if (!empty($search)) {
            $sql .= " AND m.name LIKE :search";
        }
        if (!empty($category)) {
            $sql .= " AND m.category_id = :category";
        }

        $sql .= " ORDER BY m.name ASC";

        $query = $conn->prepare($sql);

        if (!empty($search)) {
            $search = "%$search%";
            $query->bindParam(":search", $search);
        }
        if (!empty($category)) {
            $query->bindParam(":category", $category);
        }

        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories() {
        $conn = $this->connect();
        $sql = "SELECT * FROM categories ORDER BY category_name ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
