<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Products</h5>
                    <a href="#" class="btn btn-primary">Add New Product</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="productsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <!-- Products will be loaded here via AJAX -->
                                <tr id="loading-row">
                                    <td colspan="9" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- / Content -->

<!-- Add this script at the end of the file -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchProducts();
});

function fetchProducts() {
    console.log('Fetching products...');
    fetch('/api/products')
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                displayProducts(data.data);
            } else {
                showError('Failed to load products: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error fetching products:', error);
            showError(`Error loading products: ${error.message}`);
        });
}

function displayProducts(products) {
    const tableBody = document.getElementById('productsTableBody');
    tableBody.innerHTML = '';
    
    if (products.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center">No products found</td>
            </tr>
        `;
        return;
    }
    
    products.forEach(product => {
        const statusClass = product.status === 'published' ? 'success' : 
                           (product.status === 'draft' ? 'warning' : 'secondary');
        
        let imageHtml = '';
        if (product.image) {
            imageHtml = `<img src="/storage/${product.image}" alt="${product.name}" width="50">`;
        } else {
            imageHtml = `
                <div class="avatar">
                    <div class="avatar-initial bg-label-primary rounded">
                        ${product.name.charAt(0)}
                    </div>
                </div>
            `;
        }
        
        tableBody.innerHTML += `
            <tr>
                <td>${product.id}</td>
                <td>${imageHtml}</td>
                <td>${product.name}</td>
                <td>$${parseFloat(product.price).toFixed(2)}</td>
                <td>${product.stock}</td>
                <td>${product.category ? product.category.name : 'N/A'}</td>
                <td>${product.brand ? product.brand.name : 'N/A'}</td>
                <td>
                    <span class="badge bg-label-${statusClass}">
                        ${product.status.charAt(0).toUpperCase() + product.status.slice(1)}
                    </span>
                </td>
                <td>
                    <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="ri-more-2-fill"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">
                                <i class="ri-pencil-line me-2"></i> Edit
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="ri-delete-bin-line me-2"></i> Delete
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    });
}

function showError(message) {
    const tableBody = document.getElementById('productsTableBody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="9" class="text-center text-danger">${message}</td>
        </tr>
    `;
}
</script>