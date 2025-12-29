@props(['id' => 0, 'existingItems' => []])

<div class="row g-2">
    
    <div class="col-12">
        <label class="form-label small fw-bold text-muted">Item Name</label>
        <select id="existingItemSelect-{{ $id }}" name="attach_items[][item_id]" class="form-select">
            <option value="" disabled selected>-- Select Item --</option>
            @foreach($existingItems as $item)
                <option value="{{ $item->id }}" data-name="{{ $item->name }}">
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label for="existingItemQuantity-{{ $id }}" class="form-label small fw-bold text-muted">Quantity</label>
        <div class="input-group">
            <input type="number" id="existingItemQuantity-{{ $id }}" name="attach_items[][quantity]" class="form-control" placeholder="e.g. 5" min="0">
        </div>
    </div>

    <div class="col-12 mt-3">
        <button type="button" onclick="addExistingItem('{{ $id }}')" class="btn btn-secondary w-100">
            <i class="bi bi-box-arrow-in-down me-1"></i> Add Existing
        </button>
    </div>
</div>