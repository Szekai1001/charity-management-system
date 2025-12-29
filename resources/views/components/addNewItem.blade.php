@props(['id' => 0])

<div class="row g-2">
    
    <div class="col-12">
        <label for="newItemName-{{ $id }}" class="form-label small fw-bold text-muted">Item Name</label>
        <input type="text" id="newItemName-{{ $id }}" class="form-control" placeholder="e.g. Milo 3-in-1">
    </div>

    <div class="col-6">
        <label for="newItemQuantity-{{ $id }}" class="form-label small fw-bold text-muted">Quantity</label>
        <input type="number" id="newItemQuantity-{{ $id }}" class="form-control" placeholder="0" min="1">
    </div>

    <div class="col-6">
        <label for="newItemPrice-{{ $id }}" class="form-label small fw-bold text-muted">Price (RM)</label>
        <input type="number" id="newItemPrice-{{ $id }}" class="form-control" placeholder="0.00" min="0" step="0.01">
    </div>

    <div class="col-12 mt-3">
        <button type="button" onclick="addNewItem('{{ $id ?? 0 }}')" class="btn btn-primary w-100">
            <i class="bi bi-plus-lg me-1"></i> Add New Item
        </button>
    </div>
</div>