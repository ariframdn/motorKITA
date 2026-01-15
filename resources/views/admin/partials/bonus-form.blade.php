<div class="mb-3">
    <label class="form-label">Nama Bonus <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ $bonus->name ?? '' }}" required 
           placeholder="Bonus Performance">
</div>
<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi bonus...">{{ $bonus->description ?? '' }}</textarea>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Tipe Bonus <span class="text-danger">*</span></label>
        <select name="type" class="form-select" required>
            <option value="performance" {{ ($bonus->type ?? 'performance') == 'performance' ? 'selected' : '' }}>Performance</option>
            <option value="holiday" {{ ($bonus->type ?? '') == 'holiday' ? 'selected' : '' }}>Holiday</option>
            <option value="custom" {{ ($bonus->type ?? '') == 'custom' ? 'selected' : '' }}>Custom</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Min. Service/Hari (Opsional)</label>
        <input type="number" name="min_services" class="form-control" value="{{ $bonus->min_services ?? '' }}" 
               min="1" placeholder="5">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Jenis Bonus <span class="text-danger">*</span></label>
        <select name="bonus_type" class="form-select" required>
            <option value="fixed" {{ ($bonus->bonus_type ?? 'fixed') == 'fixed' ? 'selected' : '' }}>Fixed (Rp)</option>
            <option value="percentage" {{ ($bonus->bonus_type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Nilai Bonus <span class="text-danger">*</span></label>
        <input type="number" name="bonus_amount" class="form-control" value="{{ $bonus->bonus_amount ?? '' }}" required 
               step="0.01" min="0" placeholder="500000">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Tanggal Mulai (Opsional)</label>
        <input type="date" name="effective_date" class="form-control" 
               value="{{ isset($bonus->effective_date) ? \Carbon\Carbon::parse($bonus->effective_date)->format('Y-m-d') : '' }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Tanggal Berakhir (Opsional)</label>
        <input type="date" name="expiry_date" class="form-control" 
               value="{{ isset($bonus->expiry_date) ? \Carbon\Carbon::parse($bonus->expiry_date)->format('Y-m-d') : '' }}">
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="is_active" class="form-select">
        <option value="1" {{ ($bonus->is_active ?? true) ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ isset($bonus->is_active) && !$bonus->is_active ? 'selected' : '' }}>Nonaktif</option>
    </select>
</div>
