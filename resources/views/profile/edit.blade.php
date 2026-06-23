@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 dark:border-gray-800 dark:bg-white/[0.03]">






    {{-- PROFILE CARD + MODAL --}}
    <x-profile.update-profile-card :user="$user" />
    <x-profile.update-password-card />


</div>

@endsection