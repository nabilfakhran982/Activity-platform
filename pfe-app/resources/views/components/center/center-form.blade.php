@props(['center' => null, 'formId' => 'center-form'])

<form id="{{ $formId }}" data-id="{{ $center?->id }}" class="w-full">
    @csrf

    <div class="form-group">
        <label class="form-label">Center Name</label>
        <input type="text" name="name" class="form-input" placeholder="e.g. Dragon Academy" value="{{ $center?->name }}"
            required>
        <p class="error-msg" id="{{ $formId }}-err-name"></p>
    </div>

    <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-input"
            placeholder="Tell us about your center...">{{ $center?->description }}</textarea>
    </div>

    <div class="form-group">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-input" placeholder="e.g. Rue Gouraud, Gemmayzeh"
            value="{{ $center?->address }}" required>
        <p class="error-msg" id="{{ $formId }}-err-address"></p>
    </div>

    <div class="form-group">
        <label class="form-label">City</label>
        <input type="text" name="city" class="form-input" placeholder="e.g. Beirut" value="{{ $center?->city }}"
            list="{{ $formId }}-cities" required>
        <datalist id="{{ $formId }}-cities">
            @foreach(require app_path('Data/cities.php') as $city)
                <option value="{{ $city }}" />
            @endforeach
        </datalist>
        <p class="error-msg" id="{{ $formId }}-err-city"></p>
    </div>


    <div class="form-group">
        <label class="form-label">Phone</label>
        <input type="tel" name="phone" class="form-input" placeholder="+961 70 000 000" value="{{ $center?->phone }}">
    </div>

    <div class="form-group">
        <label class="form-label">Location on Map</label>
        <p class="text-xs mb-2" style="color:#a09890">Click on the map to pin your center's location</p>
        <div id="{{ $formId }}-map"
            style="height:220px;border-radius:12px;border:1px solid #E8E5DF;overflow:hidden;z-index:1"></div>
        <input type="hidden" name="lat" id="{{ $formId }}-lat" value="{{ $center?->lat }}">
        <input type="hidden" name="lng" id="{{ $formId }}-lng" value="{{ $center?->lng }}">
        <p class="text-xs mt-2" id="{{ $formId }}-coords" style="color:#a09890">
            @if($center?->lat)
                📍 {{ $center->lat }}, {{ $center->lng }}
            @else
                No location selected
            @endif
        </p>
    </div>

    <button type="submit" class="search-btn w-full py-3 text-sm mt-4 font-medium">
        {{ $center ? 'Update Center' : 'Submit' }}
    </button>
</form>
