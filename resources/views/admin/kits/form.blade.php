@php
    $featuresText = old('features_text', implode(PHP_EOL, $kit->features ?? []));
@endphp

<div class="admin-form-grid">
    <label>
        <span>Nombre del kit</span>
        <input type="text" name="name" value="{{ old('name', $kit->name) }}" placeholder="Ej. Kit residencial de 4 cámaras" required>
        @error('name') <em>{{ $message }}</em> @enderror
    </label>
    <label>
        <span>Slug <small>(opcional)</small></span>
        <input type="text" name="slug" value="{{ old('slug', $kit->slug) }}" placeholder="Se genera desde el nombre">
        @error('slug') <em>{{ $message }}</em> @enderror
    </label>
    <label>
        <span>Número de cámaras</span>
        <input type="number" name="camera_count" value="{{ old('camera_count', $kit->camera_count) }}" min="1" max="64">
        @error('camera_count') <em>{{ $message }}</em> @enderror
    </label>
    <label>
        <span>Precio MXN</span>
        <input type="number" name="price" value="{{ old('price', $kit->price) }}" min="0" step="0.01" required>
        @error('price') <em>{{ $message }}</em> @enderror
    </label>
    <label class="admin-form-full">
        <span>Descripción breve</span>
        <textarea name="description" rows="3" maxlength="1000">{{ old('description', $kit->description) }}</textarea>
        @error('description') <em>{{ $message }}</em> @enderror
    </label>
    <label class="admin-form-full">
        <span>Características <small>(una por línea)</small></span>
        <textarea name="features_text" rows="8" maxlength="4000" placeholder="4 cámaras de 2 MP&#10;Grabador con acceso remoto&#10;Disco duro de 1 TB">{{ $featuresText }}</textarea>
        @error('features_text') <em>{{ $message }}</em> @enderror
    </label>
    <label>
        <span>Orden de aparición</span>
        <input type="number" name="sort_order" value="{{ old('sort_order', $kit->sort_order ?? 0) }}" min="0" max="999">
        @error('sort_order') <em>{{ $message }}</em> @enderror
    </label>
    <div class="check-stack">
        <label class="check-control"><input type="checkbox" name="installation_included" value="1" @checked(old('installation_included', $kit->exists ? $kit->installation_included : true))><span>Instalación incluida</span></label>
        <label class="check-control"><input type="checkbox" name="featured" value="1" @checked(old('featured', $kit->featured))><span>Marcar como recomendado</span></label>
        <label class="check-control"><input type="checkbox" name="active" value="1" @checked(old('active', $kit->active))><span>Publicar en el sitio</span></label>
    </div>
</div>

<div class="form-actions">
    <button class="admin-button" type="submit">Guardar kit</button>
    <a href="{{ route('admin.kits.index') }}">Cancelar</a>
</div>
