@extends('layouts.guest')

@section('title', $title ?? 'VYRON')

@section('content')
    {{ $slot }}
@endsection