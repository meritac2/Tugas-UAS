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
