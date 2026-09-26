<div class="admin-form-grid">
    <label><span>Nombre</span><input name="name" value="{{ old('name', $prospect->name) }}" maxlength="160">@error('name')<em>{{ $message }}</em>@enderror</label>
    <label><span>Negocio</span><input name="business" value="{{ old('business', $prospect->business) }}" maxlength="160">@error('business')<em>{{ $message }}</em>@enderror</label>
    <label><span>Tipo de solicitud</span><select name="request_type" required>@foreach (\App\Models\Prospect::REQUEST_TYPES as $key => $label)<option value="{{ $key }}" @selected(old('request_type', $prospect->request_type ?: 'project') === $key)>{{ $label }}</option>@endforeach</select></label>
    <label><span>Teléfono</span><input name="phone" value="{{ old('phone', $prospect->phone) }}" maxlength="40">@error('phone')<em>{{ $message }}</em>@enderror</label>
    <label><span>Correo</span><input type="email" name="email" value="{{ old('email', $prospect->email) }}">@error('email')<em>{{ $message }}</em>@enderror</label>
    <label><span>Servicio</span><select name="service_interest"><option value="">Por definir</option>@foreach (config('sentriq.services') as $key => $service)<option value="{{ $key }}" @selected(old('service_interest', $prospect->service_interest) === $key)>{{ $service['name'] }}</option>@endforeach</select></label>
    <label><span>Municipio o zona</span><input name="municipality" value="{{ old('municipality', $prospect->municipality) }}" maxlength="120"></label>
    <label><span>Fuente</span><select name="source" required>@foreach (\App\Models\Prospect::SOURCES as $key => $label)<option value="{{ $key }}" @selected(old('source', $prospect->source ?: 'whatsapp') === $key)>{{ $label }}</option>@endforeach</select></label>
    <label><span>Campaña</span><input name="campaign" value="{{ old('campaign', $prospect->campaign) }}" maxlength="255"></label>
    <label><span>Etapa</span><select name="stage" required>@foreach (\App\Models\Prospect::STAGES as $key => $label)<option value="{{ $key }}" @selected(old('stage', $prospect->stage) === $key)>{{ $label }}</option>@endforeach</select></label>
    <label><span>Responsable</span><select name="assigned_user_id"><option value="">Sin asignar</option>@foreach ($users as $user)<option value="{{ $user->id }}" @selected((string) old('assigned_user_id', $prospect->assigned_user_id) === (string) $user->id)>{{ $user->name }}</option>@endforeach</select></label>
    <label><span>Siguiente seguimiento</span><input type="datetime-local" name="next_follow_up_at" value="{{ old('next_follow_up_at', optional($prospect->next_follow_up_at)->format('Y-m-d\TH:i')) }}"></label>
    <label><span>Último contacto</span><input type="datetime-local" name="last_contact_at" value="{{ old('last_contact_at', optional($prospect->last_contact_at)->format('Y-m-d\TH:i')) }}"></label>
    <label class="admin-form-full"><span>Descripción breve</span><textarea name="description" rows="5" maxlength="4000">{{ old('description', $prospect->description) }}</textarea></label>
</div>
<p class="form-help">Basta un nombre o negocio y al menos un teléfono o correo. Los cambios de etapa quedan registrados en la línea de tiempo.</p>
<div class="form-actions"><button class="admin-button" type="submit">Guardar prospecto</button><a href="{{ route('admin.prospects.index') }}">Cancelar</a></div>
