@extends('layouts.admin')

@section('title', 'Setings')



@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Setings</h1>
    <p class="text-gray-600 mt-1">Manage all Setings.</p>
</div>


 
        <div class="bg-white p-6 rounded-lg shadow">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-left">Vehicle</th>
                        <th class="text-left">Driver</th>
                    </tr>
                </thead>
           
            </table>
     
  
    </div>
@push('scripts')

@endpush
@endsection