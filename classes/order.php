<?php
require_once "meatshopDB.php"; 

class Order extends Database {

    // Get all orders
    public function getAllOrders() {
        $conn = $this->connect();
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";
        $result = $conn->query($sql);

        $orders = [];
        while ($row = $result->fetch()) {
            $orders[] = $row;
        }
        // Auto update logic
        $today = date('Y-m-d');

        foreach ($orders as &$order) {
            if ($order['status'] == 'Pending' && $order['delivery_date'] == $today) {
                $this->updateOrderStatus($order['id'], 'Processing');
                $order['status'] = 'Processing';
            }

            if ($order['status'] == 'Processing' && $order['delivery_date'] < $today) {
                $this->updateOrderStatus($order['id'], 'Completed');
                $order['status'] = 'Completed';
            }
    }
        return $orders;
    }

    // ✅ Get counts for dashboard
    public function getOrderCounts() {
    $conn = $this->connect();
    $sql = "
        SELECT 
            SUM(status = 'Pending') AS pending,
            SUM(status = 'Processing') AS processing,
            SUM(status = 'Completed') AS completed,
            SUM(status = 'Cancelled') AS cancelled,
            SUM(DATE(created_at) = CURDATE()) AS today
        FROM orders
    ";
    $result = $conn->query($sql);
    return $result->fetch();
}

    // Get single order
    public function getOrderById($id) {
        $conn = $this->connect();
        $id = (int)$id;
        $sql = "SELECT * FROM orders WHERE id = $id LIMIT 1";
        $result = $conn->query($sql);
        return $result->fetch();
    }

    // Get order items
    public function getOrderItems($order_id) {
        $conn = $this->connect();
        $order_id = (int)$order_id;
        $sql = "SELECT product_name, quantity, price FROM order_items WHERE order_id = $order_id";
        $result = $conn->query($sql);

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = $row;
        }
        return $items;
    }

    // Update status
    public function updateOrderStatus($order_id, $new_status) {
        $conn = $this->connect();
        $sql = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
        return $conn->query($sql);
    }

    // ✅ Place a new order (fixed order_status → status)
    public function placeOrder($customer_name, $customer_phone, $customer_address, $delivery_date, $delivery_time, $notes, $cart) {
        $conn = $this->connect();
        $total_amount = 0;

        foreach ($cart as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        $sql_order = "INSERT INTO orders 
            (customer_name, customer_phone, customer_address, total_amount, status, delivery_date, delivery_time, notes, created_at)
            VALUES ('$customer_name', '$customer_phone', '$customer_address', '$total_amount', 'Pending', '$delivery_date', '$delivery_time', '$notes', NOW())";

        if ($conn->query($sql_order)) {
            $order_id = $conn->insert_id;

            foreach ($cart as $item) {
                $product_id = $item['id'];
                $product_name = $item['name'];
                $quantity = $item['quantity'];
                $price = $item['price'];

                $sql_item = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price)
                             VALUES ('$order_id', '$product_id', '$product_name', '$quantity', '$price')";
                $conn->query($sql_item);
            }

            return $order_id;
        } else {
            return false;
        }
    }
}
?>
