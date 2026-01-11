@extends('layout.admin')
@include('components.alerts')
@section('content')
<!-- Manage Package -->
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<!-- Hidden JSON holder for JS -->
<span id="existItem" data-items='@json($existingItems ?? [])' style="display:none;"></span>

<!-- Nav Tabs -->
<ul class="nav nav-tabs mb-4" id="packageTab" role="tablist">
    <li class="nav-item" role="ation">
        <a class="nav-link {{ $activeTab == 'addNewPackage' ? 'active' : '' }}" href="?tab=addNewPackage">
            Add New Package
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $activeTab == 'editExistPackage' ? 'active' : '' }}" href="?tab=editExistPackage">
            Edit Existing Package
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $activeTab == 'editItem' ? 'active' : '' }}" href="?tab=editItem">
            Edit Item
        </a>
    </li>
</ul>

<div class="tab-content me-4" id="packageTabContent">

    <!-- Add New Package -->
    <div class="tab-pane fade {{ $activeTab == 'addNewPackage' ? 'show active' : '' }}" id="addNewPackage" role="tabpanel">

        <div class="card shadow-sm border-0">

            <form id="addPackageForm" method="POST" action="{{ route('packages.store') }}">
                @csrf

                <div class="row g-0">
                    <div class="col-lg-8 p-4 bg-white">
                        <h5 class="fw-bold mb-4 text-dark">Package Information</h5>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Package Name</label>
                                <input type="text" id="addPackageName" name="package_name" class="form-control" placeholder="Name of the package" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Details about this package" required></textarea>
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <h5 class="fw-bold mb-3 text-dark">Add Contents</h5>
                        <div class="row g-3">
                            @if($existingItems->isNotEmpty())
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light h-100">
                                    <label class="form-label small text-muted text-uppercase fw-bold">Select Existing</label>
                                    <x-addExistItem :id="'add'" :existingItems="$existingItems" />
                                </div>
                            </div>
                            @endif

                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light h-100">
                                    <label class="form-label small text-muted text-uppercase fw-bold">Create New</label>
                                    <x-addNewItem :id="'add'" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 bg-light border-start p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-4">
                                <h5 class="fw-bold mb-0">Summary</h5>
                                <span class="badge bg-secondary ms-auto">Preview</span>
                            </div>

                            <div class="packageItemList">
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-basket3 display-6 mb-2 opacity-50"></i>
                                    <p class="small mb-0">Newly added items will appear here before saving.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                                Save Package
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>


    <!-- Edit Existing Package -->
    <div class="tab-pane fade {{ $activeTab == 'editExistPackage' ? 'show active' : '' }}" id="editPackageAction" role="tabpanel">

        <div class="card shadow-sm border-0">

            <div class="row g-0">

                <div class="col-lg-8 p-4 bg-white">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-dark mb-0">Manage Existing Package</h5>

                        <form id="deletePackageForm" action="{{ route('packages.destroy') }}" method="POST" class="d-none">
                            @csrf
                            <input type="hidden" name="package_id" id="deletePackageId">
                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this package?')">
                                <i class="bi bi-trash me-1"></i> Delete Package
                            </button>
                        </form>
                    </div>

                    <div class="mb-4">
                        <label for="packageSelect-edit" class="form-label fw-semibold text-dark">Select Package to Edit</label>
                        <select name="package_id" id="packageSelect-edit" class="form-select" style="cursor: pointer;">
                            <option value="" selected disabled>-- Choose a package --</option>
                            @foreach ($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="text-muted opacity-25 my-4">

                    <form id="editPackageForm" method="POST" action="{{ route('packages.updateItems') }}">
                        @csrf
                        <input type="hidden" name="package_id" id="selectedPackageId">

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Package Description</label>
                            <textarea name="description" id="package_description" class="form-control" rows="3" placeholder="Description will load here..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Current Items in Database</label>
                            <div class="table-responsive border rounded">
                                <table id="packageItems" class="table table-sm table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Item</th>
                                            <th>Qty</th>
                                            <th>Subtotal</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-muted text-center py-4 small">
                                                Select a package above to load items.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-plus-circle me-2"></i>Add More Items</h6>

                        <div class="row g-3">
                            @if($existingItems->isNotEmpty())
                            <div class="col-md-6">
                                <div class="card p-3 bg-light">
                                    <label class="form-label small text-muted fw-bold">FROM INVENTORY</label>
                                    <x-addExistItem :id="'edit'" :existingItems="$existingItems" />
                                </div>
                            </div>
                            @endif

                            <div class="col-md-6">
                                <div class="card p-3 bg-light">
                                    <label class="form-label small text-muted fw-bold">CREATE NEW</label>
                                    <x-addNewItem :id="'edit'" />
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="col-lg-4 bg-light border-start p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Changes Preview</h5>
                            <span class="badge bg-warning text-dark ms-auto">Unsaved</span>
                        </div>

                        <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-1">Important!</h6>
                                    <p class="mb-0 small lh-sm">
                                        Any items you add or remove here are <strong>not saved</strong> until you click the <span class="fw-bold text-decoration-underline">Save Changes</span> button below.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="packageItemList">
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-basket3 display-6 mb-2 opacity-50"></i>
                                <p class="small mb-0">Newly added items will appear here before saving.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" form="editPackageForm" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            <i class="bi bi-check-lg me-2"></i>Save Changes
                        </button>
                        <small class="d-block text-center text-muted mt-2" style="font-size: 0.75rem;">
                            Updates description & adds new items
                        </small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit Items -->
    <div class="tab-pane fade {{ $activeTab == 'editItem' ? 'show active' : '' }}" id="editItemAction" role="tabpanel">

        <div class="row mt-4 g-4 justify-content-center mb-5">

            <!-- Top 3 Expensive Items -->
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card shadow-sm h-100 bg-white rounded-3 border-bottom border-4">
                    <div class="card-body p-3 text-center">

                        <i class="bi bi-currency-dollar text-success fs-3 mb-2 d-block"></i>
                        <p class="text-secondary text-uppercase small fw-bold mb-2 border-bottom pb-2">Top 3 Expensive Items</p>

                        <ul class="list-unstyled mb-0 space-y-2">
                            @foreach($topThreeExpensive as $item)
                            <li class="bg-success-subtle bg-opacity-10 p-1 rounded-2 mb-2">
                                <span class="d-block fw-semibold text-dark">{{ $item->name }}</span>
                                {{-- Smaller, prominent price display --}}
                                <span class="d-block fs-6 fw-bolder text-success">RM {{ number_format($item->estimated_price, 2) }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Top 3 High Used Items -->
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card shadow-sm h-100 bg-white rounded-3 border-bottom border-4">
                    <div class="card-body p-3 text-center">

                        <i class="bi bi-box-fill text-primary fs-3 mb-2 d-block"></i>
                        <p class="text-secondary text-uppercase small fw-bold mb-2 border-bottom pb-2">Top 3 High Used Items</p>

                        <ul class="list-unstyled mb-0 space-y-2">
                            @foreach($topHighUsedItem as $item)
                            <li class="bg-primary-subtle bg-opacity-10 p-1 rounded-2 mb-2">
                                <span class="d-block fw-semibold text-dark">{{ $item->name }}</span>
                                {{-- Smaller, prominent quantity display --}}
                                <span class="d-block fs-6 fw-bolder text-primary">{{ $item->total_quantity_used ?? 0 }} Units</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Top 3 Low Used Items -->
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card shadow-sm h-100 bg-white rounded-3 border-bottom border-4">
                    <div class="card-body p-3 text-center">

                        <i class="bi bi-archive-fill text-warning fs-3 mb-2 d-block"></i>
                        <p class="text-secondary text-uppercase small fw-bold mb-2 border-bottom pb-2">Top 3 Low Used Items</p>

                        <ul class="list-unstyled mb-0 space-y-2">
                            @foreach($topRareUsedItem as $item)
                            <li class="bg-warning-subtle bg-opacity-10 p-1 rounded-2 mb-2">
                                <span class="d-block fw-semibold text-dark">{{ $item->name }}</span>
                                {{-- Smaller, prominent quantity display --}}
                                <span class="d-block fs-6 fw-bolder text-warning">{{ $item->total_quantity_used ?? 0 }} Units</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <div class="card shadow-sm border-0 rounded-4 p-4">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-box-seam me-2 text-primary"></i> Item Management
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="col-1">#</th>
                            <th class="col-2">Item Name</th>
                            <th>
                                <a href="{{ route('admin.packages', ['tab' => 'editItem','sort' => 'price','order' => request('sort') === 'price' && request('order') === 'asc' ? 'desc' : 'asc']) }}"
                                    class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                    <span>Price (RM)</span>
                                    <!-- show arrow for all sortable columns -->
                                    <i class="bi fs-5 {{ request('sort') === 'price' ? (request('order') === 'asc' ? 'bi-arrow-up-short text-dark' : 'bi-arrow-down-short text-dark') : 'bi-arrow-down-up text-secondary fs-6' }}">
                                    </i>
                                </a>
                            </th>

                            <th class="col-6">Used In Packages</th>

                            <th>
                                <a href="{{ route('admin.packages', ['tab' => 'editItem','sort' => 'quantity','order' => request('sort') === 'quantity' && request('order') === 'asc' ? 'desc' : 'asc']) }}"
                                    class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                    <span>Quantity</span>
                                    <i class="bi fs-5 {{ request('sort') === 'quantity' ? (request('order') === 'asc' ? 'bi-arrow-up-short text-dark' : 'bi-arrow-down-short text-dark') : 'bi-arrow-down-up text-secondary fs-6' }}">
                                    </i>
                                </a>
                            </th>
                            <th class="col-1">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($existingItems as $existingItem)
                        <tr id="item-row-{{ $existingItem->id }}">

                            <td class="fw-semibold">{{ $loop->iteration }}</td>

                            <td class="fw-semibold text-dark">
                                <i class="bi bi-tag-fill text-info me-1"></i>
                                {{ $existingItem->name }}
                            </td>

                            <td>{{ $existingItem->estimated_price }}</td>

                            <td>
                                @if($existingItem->packages->count() > 0)
                                @foreach($existingItem->packages as $pkg)
                                <span class="badge bg-info text-dark me-1 mb-1">
                                    <i class="bi bi-box-seam me-1"></i>{{ $pkg->name }}
                                </span>
                                @endforeach
                                @else
                                <span class="text-muted">— Not used —</span>
                                @endif
                            </td>

                            <td>{{ $existingItem->total_quantity }}</td>

                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#priceUpdate-{{$existingItem->id}}">Edit</button>
                                    <button type="button"
                                        data-item-id="{{ $existingItem->id }}"
                                        class="btn btn-outline-danger btn-sm rounded-3 delete-item-btn">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-muted text-center py-3">
                                <i class="bi bi-inbox text-muted me-1"></i> No items found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        @foreach($existingItems as $existingItem)
        <form action="{{ route('price.update', $existingItem->id) }}" method="POST">
            @csrf
            <div class="modal fade" tabindex="-1" id="priceUpdate-{{$existingItem->id}}">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit prices: {{$existingItem->name}}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label for="updated_price_{{ $existingItem->id }}" class="form-label">Enter new price (RM):</label>
                            <input type="number" name="updated_price" value="{{ $existingItem->estimated_price }}" class="form-control" id="updated_price_{{ $existingItem->id }}" step="0.01" min="0">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @endforeach
    </div>


</div>


<script>
    let itemsByForm = {
        add: [], // for addPackageForm
        edit: [] // for editPackageForm
    };

    // Add event listeners when document is ready
    document.addEventListener('DOMContentLoaded', function() {

        //Add New Package Form Check
        const addForm = document.getElementById('addPackageForm');
        addForm.addEventListener('submit', function(e) {
            if (!prepareItemsForSubmit('add')) {
                e.preventDefault();
            }
        });

        // Edit Package Form Check
        const editForm = document.getElementById('editPackageForm');
        editForm.addEventListener('submit', function(e) {
            if (!prepareItemsForSubmit('edit')) {
                e.preventDefault();
            }
        });

    });

    // Get the available items from laravel via AJAX
    document.getElementById('packageSelect-edit').addEventListener('change', function() {
        const packageId = this.value;

        const deleteForm = document.getElementById('deletePackageForm');
        const deleteInput = document.getElementById('deletePackageId');
        const selectedInput = document.getElementById('selectedPackageId');

        if (!packageId) {
            deleteForm.classList.add('d-none');
            deleteForm.classList.remove('d-block');
            return;
        }
        if (!packageId) return;

        deleteInput.value = packageId;
        selectedInput.value = packageId;

        deleteForm.classList.remove('d-none');
        deleteForm.classList.add('d-block');


        fetch(`/admin/packages/${packageId}/json`)
            // parse json string into js object
            .then(response => response.json())
            .then(data => {

                const descriptionField = document.querySelector('#package_description');
                if (descriptionField) {
                    // Use || '' to ensure it doesn't print "null" if description is empty
                    descriptionField.value = data.description || '';
                }
                const tbody = document.querySelector('#editPackageForm tbody');
                tbody.innerHTML = '';

                data.items.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.id = `row-item-${item.id}`;
                    row.innerHTML = `
                <td>${index + 1}</td>
        <td>${item.name}</td>
        <td><input type="number" name="quantities[${item.id}]" value="${item.quantity}" class="form-control form-control-sm"></td>
        <td>${item.subtotal}</td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="if (confirm('Are you sure want to delete this item?')) { removeItemFromList(${item.id}); } return false;"
>Delete</button>
        </td>
        
    `;
                    tbody.appendChild(row);
                });

                const totalRow = document.createElement('tr');
                totalRow.innerHTML = `
                <td colspan="3" class="fw-bold text-end">Total Estimated Price:</td>
                <td colspan="2" class="fw-bold">RM ${data.packagePrice.toFixed(2)}</td>
                `;
                tbody.appendChild(totalRow);
            })
            .catch(error => console.error(error));
    });

    // Delete Item ajax
    document.querySelectorAll('.delete-item-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.itemId;

            if (!confirm('Are you sure you want to delete this item?')) return;

            fetch("{{ route('item.delete') }}", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        existingItem_delete: itemId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove row from the table
                        document.getElementById(`item-row-${itemId}`).remove();
                    } else {
                        alert('Failed to delete item.');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    document.getElementById('deletedPackageForm')



    //////////////////////////////////////////////Item function///////////////////////////////////////////////////


    function itemExists(id, name) {

        let inform = itemsByForm[id].some(item => item.name.toLowerCase() === name.toLowerCase());

        return inform;
    }

    let existingItems = JSON.parse(document.getElementById('existItem').dataset.items);

    function itemExistsDatabase(name) {
        let inExisting = existingItems.some(item => item.name.toLowerCase() === name.toLowerCase());
        return inExisting;

    }

    function itemExistsTable(name) {
        const tableRows = document.querySelectorAll('#editPackageForm tbody tr');
        let inTable = Array.from(tableRows).some(row => {
            const rowName = row.querySelector('td:nth-child(2)').textContent.trim();
            return rowName.toLowerCase() === name.toLowerCase();
        });
        return inTable;
    }


    function addNewItem(id) {
        const name = document.getElementById(`newItemName-${id}`).value.trim();
        const quantity = parseFloat(document.getElementById(`newItemQuantity-${id}`).value);
        const price = parseFloat(document.getElementById(`newItemPrice-${id}`).value);

        // Check the name, quantity is in correct format
        if (!name || isNaN(quantity) || quantity <= 0 || isNaN(price) || price <= 0) {
            alert("Please complete all fields for new item with valid values.");
            return;
        }

        // Check for duplicate items in item array
        if (itemExists(id, name)) {
            alert("Item already added.");
            return;
        }

        if (itemExistsDatabase(name)) {
            alert("Item already exists in the exisiting item.");
            return;
        }

        // Push the data into item array
        itemsByForm[id].push({
            item_id: null,
            name: name,
            quantity: quantity,
            estimated_price: price,
            is_new: true
        });


        const formId = (id === 'add') ? 'addPackageForm' : 'editPackageForm';
        const form = document.getElementById(formId);
        const index = document.querySelectorAll('input[name^="new_items"]').length;

        // Create a hidden input for submit new item name
        const inputName = document.createElement('input');
        inputName.type = 'hidden';
        inputName.name = `new_items[${index}][name]`;
        inputName.value = name;

        // Create a hidden input for submit new item quantity
        const inputQty = document.createElement('input');
        inputQty.type = 'hidden';
        inputQty.name = `new_items[${index}][quantity]`;
        inputQty.value = quantity;

        const inputPrice = document.createElement('input');
        inputPrice.type = 'hidden';
        inputPrice.name = `new_items[${index}][price]`;
        inputPrice.value = price;

        form.appendChild(inputName);
        form.appendChild(inputQty);
        form.appendChild(inputPrice);

        // Clear input fields
        document.getElementById(`newItemName-${id}`).value = '';
        document.getElementById(`newItemQuantity-${id}`).value = '';
        document.getElementById(`newItemPrice-${id}`).value = '';

        renderItems(id);
    }

    function addExistingItem(id) {
        const select = document.getElementById(`existingItemSelect-${id}`);
        const itemId = select.value;
        const quantityInput = document.getElementById(`existingItemQuantity-${id}`);
        const quantity = parseFloat(quantityInput.value);


        // Check if not item selected
        if (!itemId) {
            alert("Please select an item.");
            return;
        }

        // Check if not quantity selected or invalid
        if (!quantity || quantity <= 0) {
            alert("Please enter a valid quantity.");
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const name = selectedOption.getAttribute('data-name');


        // Check for duplicate items
        if (itemExists(id, name)) {
            alert("Item already added.");
            return;
        }

        // Only check the table in EDIT mode
        if (id !== 'add' && itemExistsTable(name)) {
            alert("Item already added in table.");
            return;
        }


        itemsByForm[id].push({
            item_id: itemId,
            name: name,
            quantity: quantity,
            is_new: false
        });

        const formId = (id === 'add') ? 'addPackageForm' : 'editPackageForm';
        const form = document.getElementById(formId);
        const index = document.querySelectorAll('input[name^="attach_items"]').length;

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = `attach_items[${index}][item_id]`;
        inputId.value = itemId;

        const inputQty = document.createElement('input');
        inputQty.type = 'hidden';
        inputQty.name = `attach_items[${index}][quantity]`;
        inputQty.value = quantity;

        form.appendChild(inputId);
        form.appendChild(inputQty);

        // Clear input fields
        quantityInput.value = '';
        select.selectedIndex = 0;

        renderItems(id);

    }

    function renderItems(id) {
        const form = document.getElementById(`${id}PackageForm`);
        const container = form.closest('.tab-pane').querySelector('.packageItemList');
        const items = itemsByForm[id] || [];
        if (items.length === 0) {
            container.innerHTML = `
                 <div class="text-center text-muted py-5">
                                <i class="bi bi-basket3 display-6 mb-2 opacity-50"></i>
                                <p class="small mb-0">Newly added items will appear here before saving.</p>
                            </div>
            `;
            return;
        }



        const itemsHtml = items.map((item, index) => `
            <div class="card mb-2 p-2 border shadow-sm d-flex flex-row justify-content-between align-items-center">
                <div>
                    <h6>${escapeHtml(item.name)}</h6>
                    <small>Quantity: ${item.quantity}</small><br>
                  ${id === 'new' ? `<small>Price per Item: RM ${item.estimated_price}</small>` : ''}
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeItem('${id}',${index})">
                    Remove
                </button>
            </div>
        `).join('');

        container.innerHTML = `
            <h6>Package Items (${items.length}):</h6>
            ${itemsHtml}
        `;
    }

    function removeItem(id, index) {
        itemsByForm[id].splice(index, 1);
        renderItems(id);
    }



    function removeItemFromList(itemId) {
        // Remove row by ID
        const row = document.getElementById(`row-item-${itemId}`);
        if (row) row.remove();

        // Add hidden input for backend
        const form = document.getElementById('editPackageForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `remove_items[]`;
        input.value = itemId;
        form.appendChild(input);
    }

    function prepareItemsForSubmit(formType) {
        let form, packageNameInput;

        if (formType === 'add') {
            form = document.getElementById('addPackageForm');
            packageNameInput = document.getElementById('addPackageName');

            if (itemsByForm[formType].length === 0) {
                alert("Please add at least one item to the package.");
                return false;
            }
        } else if (formType === 'edit') {
            form = document.getElementById('editPackageForm');
            packageNameInput = document.getElementById('selectedPackageId');

            // Must select a package
            if (!packageNameInput.value.trim()) {
                alert("Please select a package to edit.");
                return false;
            }

            // ✅ Accept either:
            // (1) New/existing items via itemsByForm
            // OR (2) Existing rows in the table (for quantity edits)
            const hasNewOrExisting = itemsByForm[formType].length > 0;
            const tableHasRows = Array.from(document.querySelectorAll('#editPackageForm tbody tr'))
                .some(row => !row.querySelector('td').classList.contains('text-muted'));


            if (!hasNewOrExisting && !tableHasRows) {
                alert("Please keep at least one item in the package.");
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

@endsection