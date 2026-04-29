@props(['activity' => null, 'formId' => 'activity-form', 'categories' => []])

<form id="{{ $formId }}" enctype="multipart/form-data">
    @csrf

    <div class="grid md:grid-cols-2 gap-4">

        <div class="form-group md:col-span-2">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-input"
                value="{{ $activity?->title }}"
                placeholder="e.g. Kids Karate — Beginners">
            <p class="error-msg" id="{{ $formId }}-err-title"></p>
        </div>

        <div class="form-group md:col-span-2">
            <label class="form-label">Description</label>
            <textarea name="description" rows="2" class="form-input"
                placeholder="Describe your activity...">{{ $activity?->description }}</textarea>
        </div>

        <div class="form-group md:col-span-2">
            <label class="form-label">Category</label>
            <input type="hidden" name="category_id" id="{{ $formId }}-category_id"
                value="{{ $activity?->category_id }}">
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 mt-1" id="{{ $formId }}-cat-picker">
                @foreach($categories as $cat)
                    <button type="button"
                        onclick="pickCategory('{{ $formId }}', {{ $cat->id }}, this)"
                        class="cat-pick-btn flex flex-col items-center p-2 rounded-lg text-center transition"
                        style="border:2px solid {{ $activity?->category_id == $cat->id ? '#D4A350' : '#e5e7eb' }};background:{{ $activity?->category_id == $cat->id ? '#FDF8EE' : '#fff' }};cursor:pointer">
                        <img src="{{ asset('images/categories/' . $cat->icon) }}"
                             alt="{{ $cat->name }}"
                             class="w-12 h-12 mx-auto mb-3 object-contain">
                        <span class="text-xs leading-tight">{{ $cat->name }}</span>
                    </button>
                @endforeach
            </div>
            <p class="error-msg" id="{{ $formId }}-err-category_id"></p>
        </div>

        <div class="form-group">
            <label class="form-label">Level</label>
            <select name="level" class="form-input">
                <option value="">Any level</option>
                @foreach(['beginner', 'intermediate', 'advanced'] as $level)
                    <option value="{{ $level }}"
                        {{ $activity?->level === $level ? 'selected' : '' }}>
                        {{ ucfirst($level) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Price ($/session)</label>
            <input type="number" name="price" class="form-input"
                value="{{ $activity?->price }}"
                placeholder="e.g. 25" min="0" step="0.01">
            <p class="error-msg" id="{{ $formId }}-err-price"></p>
        </div>

        <div class="form-group">
            <label class="form-label">Capacity</label>
            <input type="number" name="capacity" class="form-input"
                value="{{ $activity?->capacity }}"
                placeholder="e.g. 15" min="1">
            <p class="error-msg" id="{{ $formId }}-err-capacity"></p>
        </div>

        <div class="form-group">
            <label class="form-label">Min Age</label>
            <input type="number" name="min_age" class="form-input"
                value="{{ $activity?->min_age }}"
                placeholder="optional" min="0">
        </div>

        <div class="form-group">
            <label class="form-label">Max Age</label>
            <input type="number" name="max_age" class="form-input"
                value="{{ $activity?->max_age }}"
                placeholder="optional" min="0">
        </div>

        <div class="form-group md:col-span-2">
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_private"
                    id="{{ $formId }}-is_private" value="1"
                    {{ $activity?->is_private ? 'checked' : '' }}
                    class="w-4 h-4 accent-[#D4A350]">
                <label for="{{ $formId }}-is_private" class="text-sm" style="color:#5a5751">
                    Private session (1-on-1)
                </label>
            </div>
        </div>

    </div>

    {{-- Schedules --}}
    <div class="form-group mt-2">
        <div class="flex items-center justify-between mb-3">
            <label class="form-label mb-0">Schedules</label>
            <button type="button"
                onclick="addScheduleRow('{{ $formId }}-schedules')"
                class="text-xs hover:underline" style="color:#D4A350; background:none; border:none; cursor:pointer">
                + Add slot
            </button>
        </div>
        <div id="{{ $formId }}-schedules" class="space-y-2">
            @if($activity?->schedules)
                @foreach($activity->schedules as $schedule)
                    {{-- Schedules are populated via JS for edit mode --}}
                @endforeach
            @endif
        </div>
    </div>

    {{-- Image --}}
    <div class="form-group">
        <label class="form-label">
            Image {{ $activity ? '(leave empty to keep current)' : '' }}
        </label>
        @if($activity?->images?->first())
            <div class="mb-2">
                <img src="{{ asset('storage/' . $activity->images->first()->image_path) }}"
                     alt="Current image"
                     class="w-full h-32 object-cover rounded-lg">
            </div>
        @endif
        <input type="file" name="image" accept="image/*" class="form-input" style="padding:8px">
        <p class="error-msg" id="add-activity-form-err-image"></p>
    </div>

    <button type="submit" class="search-btn w-full py-3 text-sm mt-2">
        {{ $activity ? 'Update Activity' : 'Add Activity' }}
    </button>

</form>
