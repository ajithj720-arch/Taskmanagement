@extends('layouts.app')
@section('title', 'Add User')
@section('page-title', 'Add New User')

@section('content')
<div class="mt-2 max-w-lg">
    <div class="card-bg rounded-2xl border border-white/5 p-6">
        <form id="create-user-form" method="POST" action="{{ route('users.store') }}">
            @csrf
            @include('users._form')

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" form="create-user-form"
                        class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg transition">
                    Create User
                </button>
                <a href="{{ route('users.index') }}"
                   class="text-sm text-slate-400 hover:text-white transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
