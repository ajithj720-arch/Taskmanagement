@props(['task' => null, 'users' => collect()])

<div class="space-y-4">
    {{-- Title --}}
    <div>
        <input type="text" name="title" value="{{ old('title', $task?->title) }}"
               placeholder="e.g. Launch New Campaign"
               class="input-dark w-full px-4 py-2.5 rounded-lg text-sm @error('title') border-red-500 @enderror">
        @error('title')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
    </div>

    {{-- Description --}}
    <div>
        <textarea name="description" rows="4"
                  placeholder="Task description..."
                  class="input-dark w-full px-4 py-2.5 rounded-lg text-sm resize-none @error('description') border-red-500 @enderror">{{ old('description', $task?->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
    </div>

    {{-- Priority toggle --}}
    <div>
        <p class="text-sm text-slate-300 mb-2 font-medium">Priority</p>
        <div class="flex items-center gap-2 flex-wrap" id="priority-group">
            @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High'] as $val=>$lbl)
            <label class="cursor-pointer">
                <input type="radio" name="priority" value="{{ $val }}" class="sr-only peer"
                       {{ old('priority', $task?->priority?->value ?? 'medium') === $val ? 'checked' : '' }}>
                <span class="px-4 py-1.5 rounded-lg text-sm font-medium border transition
                    peer-checked:bg-blue-500 peer-checked:border-blue-500 peer-checked:text-white
                    border-slate-600 text-slate-400 hover:border-blue-400">
                    {{ $lbl }}
                </span>
            </label>
            @endforeach
        </div>
        @error('priority')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
    </div>

    {{-- Status --}}
    <div>
        <p class="text-sm text-slate-300 mb-2 font-medium">Status</p>
        <div class="flex items-center gap-2 flex-wrap">
            @foreach(['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Completed'] as $val=>$lbl)
            <label class="cursor-pointer">
                <input type="radio" name="status" value="{{ $val }}" class="sr-only peer"
                       {{ old('status', $task?->status?->value ?? 'pending') === $val ? 'checked' : '' }}>
                <span class="px-4 py-1.5 rounded-lg text-sm font-medium border transition
                    peer-checked:bg-blue-500 peer-checked:border-blue-500 peer-checked:text-white
                    border-slate-600 text-slate-400 hover:border-blue-400">
                    {{ $lbl }}
                </span>
            </label>
            @endforeach
        </div>
        @error('status')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
    </div>

    {{-- Due Date --}}
    <div>
        <div class="relative">
            <input type="date" name="due_date"
                   value="{{ old('due_date', $task?->due_date?->format('Y-m-d')) }}"
                   class="input-dark w-full px-4 py-2.5 rounded-lg text-sm @error('due_date') border-red-500 @enderror">
        </div>
        @error('due_date')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
    </div>

    {{-- Assign To --}}
    <div>
        <p class="text-sm text-slate-300 mb-2 font-medium">Assign To</p>
        <div class="relative">
            <select name="assigned_to" class="input-dark w-full px-4 py-2.5 rounded-lg text-sm @error('assigned_to') border-red-500 @enderror">
                <option value="">Unassigned</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" @selected(old('assigned_to', $task?->assigned_to) == $user->id)>
                    {{ $user->name }}
                </option>
                @endforeach
            </select>
        </div>
        @error('assigned_to')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
    </div>
</div>
