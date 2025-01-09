# Tugas-UAS
Nama    : Merita Cahya Kurniasari
NIM     : 21.01.55.0024
Matkul  : Web Service Development

1. Buatlah folder dengan nama klien didalam folder htdocs
-  dalam folder tersebut buatlah file index.php
-  berikut coding untuk index.php
   
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <name>Daftar Shoes</name>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-group-action {
            white-space: nowrap;
        }
    </style>
</head>
<body class="container py-4">
    <h1>Daftar Shoes</h1>
    
    <div class="row mb-3">
        <div class="col">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan ID">
        </div>
        <div class="col-auto">
            <button onclick="searchshoes()" class="btn btn-primary">Cari</button>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#shoesModal">
                Tambah Shoes
            </button>
        </div>
    </div>

    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Price</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="shoesList">
        </tbody>
    </table>

    <!-- Modal for Add/Edit shoes -->
    <div class="modal fade" id="shoesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-name" id="modalname">Tambah Shoes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="shoesForm">
                        <input type="hidden" id="shoesId">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <input type="text" class="form-control" id="type" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveshoes()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_URL = 'http://localhost/rest_shoes/shoes_api.php';
        let shoesModal;

        document.addEventListener('DOMContentLoaded', function() {
            shoesModal = new bootstrap.Modal(document.getElementById('shoesModal'));
            loadshoess();
        });

        function loadshoess() {
            fetch(API_URL)
                .then(response => response.json())
                .then(shoess => {
                    const shoesList = document.getElementById('shoesList');
                    shoesList.innerHTML = '';
                    shoess.forEach(shoes => {
                        shoesList.innerHTML += `
                            <tr>
                                <td>${shoes.id}</td>
                                <td>${shoes.name}</td>
                                <td>${shoes.type}</td>
                                <td>${shoes.price}</td>
                                <td class="btn-group-action">
                                    <button class="btn btn-sm btn-warning me-1" onclick="editshoes(${shoes.id})">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteshoes(${shoes.id})">Hapus</button>
                                </td>
                            </tr>
                        `;
                    });
                })
                .catch(error => alert('Error loading shoess: ' + error));
        }

        function searchshoes() {
            const id = document.getElementById('searchInput').value;
            if (!id) {
                loadshoess();
                return;
            }
            
            fetch(`${API_URL}/${id}`)
                .then(response => response.json())
                .then(shoes => {
                    const shoesList = document.getElementById('shoesList');
                    if (shoes.message) {
                        alert('shoes not found');
                        return;
                    }
                    shoesList.innerHTML = `
                        <tr>
                            <td>${shoes.id}</td>
                            <td>${shoes.name}</td>
                            <td>${shoes.type}</td>
                            <td>${shoes.price}</td>
                            <td class="btn-group-action">
                                <button class="btn btn-sm btn-warning me-1" onclick="editshoes(${shoes.id})">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteshoes(${shoes.id})">Hapus</button>
                            </td>
                        </tr>
                    `;
                })
                .catch(error => alert('Error searching shoes: ' + error));
        }

        function editshoes(id) {
            fetch(`${API_URL}/${id}`)
                .then(response => response.json())
                .then(shoes => {
                    document.getElementById('shoesId').value = shoes.id;
                    document.getElementById('name').value = shoes.name;
                    document.getElementById('type').value = shoes.type;
                    document.getElementById('price').value = shoes.price;
                    document.getElementById('modalname').textContent = 'Edit Shoes';
                    shoesModal.show();
                })
                .catch(error => alert('Error loading shoes details: ' + error));
        }

        function deleteshoes(id) {
            if (confirm('Are you sure you want to delete this shoes?')) {
                fetch(`${API_URL}/${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    alert('shoes deleted successfully');
                    loadshoess();
                })
                .catch(error => alert('Error deleting shoes: ' + error));
            }
        }

        function saveshoes() {
            const shoesId = document.getElementById('shoesId').value;
            const shoesData = {
                name: document.getElementById('name').value,
                type: document.getElementById('type').value,
                price: document.getElementById('price').value
                stock: document.getElementById('stock').value
            };

            const method = shoesId ? 'PUT' : 'POST';
            const url = shoesId ? `${API_URL}/${shoesId}` : API_URL;

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(shoesData)
            })
            .then(response => response.json())
            .then(data => {
                alert(shoesId ? 'shoes updated successfully' : 'shoes added successfully');
                shoesModal.hide();
                loadshoess();
                resetForm();
            })
            .catch(error => alert('Error saving shoes: ' + error));
        }

        function resetForm() {
            document.getElementById('shoesId').value = '';
            document.getElementById('shoesForm').reset();
            document.getElementById('modalname').textContent = 'Tambah Shoes';
        }

        // Reset form when modal is closed
        document.getElementById('shoesModal').addEventListener('hidden.bs.modal', resetForm);
    </script>
</body>
</html>

2. Buatlah folder dengan nama rest_shoes didalam folder htdocs
- dalam folder tersebut buatlah file shoes_api.php
- berikut coding untuk shoes_api.php

<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, typeization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];
$request = [];

if (isset($_SERVER['PATH_INFO'])) {
    $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
}

function getConnection() {
    $host = 'localhost';
    $db   = 'shoesstore';
    $user = 'root';
    $pass = ''; // Ganti dengan password MySQL Anda jika ada
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}

function response($status, $data = NULL) {
    header("HTTP/1.1 " . $status);
    if ($data) {
        echo json_encode($data);
    }
    exit();
}

$db = getConnection();

switch ($method) {
    case 'GET':
        if (!empty($request) && isset($request[0])) {
            $id = $request[0];
            $stmt = $db->prepare("SELECT * FROM shoes WHERE id = ?");
            $stmt->execute([$id]);
            $shoes = $stmt->fetch();
            if ($shoes) {
                response(200, $shoes);
            } else {
                response(404, ["message" => "shoes not found"]);
            }
        } else {
            $stmt = $db->query("SELECT * FROM shoes");
            $shoes = $stmt->fetchAll();
            response(200, $shoes);
        }
        break;
    
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->name) || !isset($data->type) || !isset($data->price) || !isset($data->stock)) {
            response(400, ["message" => "Missing required fields"]);
        }
        $sql = "INSERT INTO shoes (name, type, price, stock) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        if ($stmt->execute([$data->name, $data->type, $data->price, $data->stock])) {
            response(201, ["message" => "shoes created", "id" => $db->lastInsertId()]);
        } else {
            response(500, ["message" => "Failed to create shoes"]);
        }
        break;
    
    case 'PUT':
        if (empty($request) || !isset($request[0])) {
            response(400, ["message" => "shoes ID is required"]);
        }
        $id = $request[0];
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->name) || !isset($data->type) || !isset($data->price)) {
            response(400, ["message" => "Missing required fields"]);
        }
        $sql = "UPDATE shoes SET name = ?, type = ?, price = ?,  stock = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        if ($stmt->execute([$data->name, $data->type, $data->price, $data->stock, $id])) {
            response(200, ["message" => "shoes updated"]);
        } else {
            response(500, ["message" => "Failed to update shoes"]);
        }
        break;
    
    case 'DELETE':
        if (empty($request) || !isset($request[0])) {
            response(400, ["message" => "shoes ID is required"]);
        }
        $id = $request[0];
        $sql = "DELETE FROM shoes WHERE id = ?";
        $stmt = $db->prepare($sql);
        if ($stmt->execute([$id])) {
            response(200, ["message" => "shoes deleted"]);
        } else {
            response(500, ["message" => "Failed to delete shoes"]);
        }
        break;
    
    default:
        response(405, ["message" => "Method not allowed"]);
        break;
}
?>

3. Berikut export dari http://localhost/phpmyadmin/index.php?route=/database/export&db=shoesstore

   -- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 09, 2025 at 02:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shoesstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `shoes`
--

CREATE TABLE `shoes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `price` varchar(100) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shoes`
--

INSERT INTO `shoes` (`id`, `name`, `type`, `price`, `stock`) VALUES
(1, 'Slip-Ins GO WALK 7 Mens Sneakers', 'Natural', 'Rp. 1.499.000', 26),
(2, 'Slip-Ins GO WALK 7 Mens Sneakers', 'Black', 'Rp. 1.499.000', 6),
(3, 'GO WALK 7 Mens Sneakers', 'Charcoal', 'Rp. 1.299.000', 15),
(4, 'GO RUN Elevate 2.0 Mens Sneakers', 'Brown', 'Rp. 1.199.000', 5),
(5, 'GO RUN Consistent 2.0 Mens Sneakers', 'Navy', 'Rp. 719.200', 11),
(6, 'Skechers Slip-Ins Go Walk 7 Mens Walking Shoes ', 'Navy', 'Rp. 1.199.200', 5),
(7, 'Skechers Go Walk Max Walker Womens Walking Shoes\r\n', 'Natural', 'Rp. 1.299.000', 22),
(8, 'Skechers Go Run Lite Womens Running Shoes', 'Black', 'Rp. 999.000', 7),
(9, 'SKECHERS GO WALK ARCH FIT WOMENS WALKING SHOES', 'TAUPE', 'Rp. 1.199.000', 10),
(10, 'Sepatu Running Runfalcon 5\r\n', 'Multicolor', 'Rp. 850.000', 6),
(11, 'Skechers SKX Resagrip Mens Basketball Shoes', 'Black\r\n', 'Rp. 1.999.000', 9),
(12, 'Skechers Slip-Ins Go Walk Joy Womens Sneaker ', 'Navy', 'Rp. 999.000', 5),
(13, 'GO WALK Smart 2 Womens Sneakers', 'Taupe', 'Rp. 1.099.000', 15),
(14, 'NIKE Pegasus 5 GORE-TEX Mens Waterproof Trail Running Shoes', 'Green', 'Rp. 2.669.000', 11);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shoes`
--
ALTER TABLE `shoes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shoes`
--
ALTER TABLE `shoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

   
