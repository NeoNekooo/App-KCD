@extends('errors.layout')

@section('title', 'Akses Ditolak!')
@section('code', '403')
@section('description', 'Oops! Area ini khusus pegawai berwenang.')
@section('message', $exception->getMessage() ?: 'Maaf, Role Anda tidak memiliki izin akses.')