<div class="mb-3">
    <label class="form-label">Kode <span class="text-danger">*</span></label>
    <input type="text" name="code" class="form-control" value="{{ $promo->code ?? '' }}" required 
           placeholder="PROMO50" style="text-transform: uppercase;">
</div>
<div class="mb-3">
    <label class="form-label">Nama <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ $promo->name ?? '' }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea name="description" class="form-control" rows="2">{{ $promo->description ?? '' }}</textarea>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Tipe Diskon <span class="text-danger">*</span></label>
        <select name="discount_type" class="form-select" required>
            <option value="percentage" {{ ($promo->discount_type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
            <option value="fixed" {{ ($promo->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Nilai Diskon <span class="text-danger">*</span></label>
        <input type="number" name="discount_value" class="form-control" value="{{ $promo->discount_value ?? '' }}" required 
               step="0.01" min="0">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Min. Belanja (Opsional)</label>
        <input type="number" name="min_purchase" class="form-control" value="{{ $promo->min_purchase ?? '' }}" 
               step="0.01" min="0">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Max. Diskon (Opsional)</label>
        <input type="number" name="max_discount" class="form-control" value="{{ $promo->max_discount ?? '' }}" 
               step="0.01" min="0">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
        <input type="date" name="start_date" class="form-control" 
               value="{{ isset($promo->start_date) ? \Carbon\Carbon::parse($promo->start_date)->format('Y-m-d') : '' }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Tanggal Akhir <span class="text-danger">*</span></label>
        <input type="date" name="end_date" class="form-control" 
               value="{{ isset($promo->end_date) ? \Carbon\Carbon::parse($promo->end_date)->format('Y-m-d') : '' }}" required>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Limit Penggunaan (Opsional)</label>
        <input type="number" name="usage_limit" class="form-control" value="{{ $promo->usage_limit ?? '' }}" 
               min="1">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select">
            <option value="1" {{ ($promo->is_active ?? true) ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ isset($promo->is_active) && !$promo->is_active ? 'selected' : '' }}>Nonaktif</option>
        </select>
    </div>
</div>
