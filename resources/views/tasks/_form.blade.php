<div>
    <x-input-label for="title" :value="__('Title')" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
        value="{{ old('title', $task?->title) }}" required autofocus />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" :value="__('Description')" />
    <textarea id="description" name="description" rows="4"
        class="mt-1 block w-full border-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-lg shadow-sm">{{ old('description', $task?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="due_date" :value="__('Due Date')" />
    <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full"
        value="{{ old('due_date', $task?->due_date?->format('Y-m-d')) }}" />
    <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
</div>

<div class="mt-4 grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status"
            class="mt-1 block w-full border-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-lg shadow-sm">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $task?->status?->value) === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="priority" :value="__('Priority')" />
        <select id="priority" name="priority"
            class="mt-1 block w-full border-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-lg shadow-sm">
            @foreach ($priorities as $priority)
                <option value="{{ $priority->value }}" @selected(old('priority', $task?->priority?->value) === $priority->value)>
                    {{ $priority->label() }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('priority')" class="mt-2" />
    </div>
</div>
