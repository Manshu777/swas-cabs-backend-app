@extends('layouts.admin')

@section('title', 'Driver Documents')

@section('content')
<div class="container mx-auto px-4 py-6">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Driver Documents</h1>
    <div class="text-sm text-gray-600">Total: {{ $documents->total() ??  $documents->count() }}</div>
  </div>


  <div class="bg-white shadow rounded-lg overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle RC</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Insurance</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Police Verif.</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>

      <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($documents as $doc)
          <tr>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ $loop->iteration + (($documents->currentPage() - 1) * $documents->perPage() ?? 0) }}
            
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-sm">
              @if(method_exists($doc, 'user') && $doc->user)
                <div class="font-medium text-gray-800">{{ $doc->user->name }}</div>
                <div class="text-xs text-gray-500">{{ $doc->user->email ?? $doc->user->phone }}</div>
              @else
                <div class="text-sm text-gray-700">User ID: {{ $doc->user_id }}</div>
              @endif
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-sm">
              <div class="mb-1 text-sm text-gray-700">{{ $doc->license_number }}</div>
              <div class="flex items-center space-x-3">
                @if($doc->license_image)
                  <a href="{{ asset('storage/' . $doc->license_image) }}" target="_blank" class="inline-block">
                    <img src="{{ asset('storage/' . $doc->license_image) }}" alt="license" class="w-16 h-10 object-cover rounded border">
                  </a>
                @else
                  <div class="w-16 h-10 flex items-center justify-center rounded border text-xs text-gray-400">No image</div>
                @endif
              </div>
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-sm">
              <div class="mb-1 text-sm text-gray-700">{{ $doc->vehicle_rc_number }}</div>
              @if($doc->vehicle_rc_image)
                <a href="{{ asset('storage/' . $doc->vehicle_rc_image) }}" target="_blank" class="inline-block">
                  <img src="{{ asset('storage/' . $doc->vehicle_rc_image) }}" alt="rc" class="w-16 h-10 object-cover rounded border">
                </a>
              @else
                <div class="w-16 h-10 flex items-center justify-center rounded border text-xs text-gray-400">No image</div>
              @endif
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-sm">
              <div class="mb-1 text-sm text-gray-700">{{ $doc->insurance_number }}</div>
              @if($doc->insurance_image)
                <a href="{{ asset('storage/' . $doc->insurance_image) }}" target="_blank" class="inline-block">
                  <img src="{{ asset('storage/' . $doc->insurance_image) }}" alt="insurance" class="w-16 h-10 object-cover rounded border">
                </a>
              @else
                <div class="w-16 h-10 flex items-center justify-center rounded border text-xs text-gray-400">No image</div>
              @endif
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-sm">
              @if($doc->police_verification_image)
                <a href="{{ asset('storage/' . $doc->police_verification_image) }}" target="_blank" class="inline-block">
                  <img src="{{ asset('storage/' . $doc->police_verification_image) }}" alt="police" class="w-16 h-10 object-cover rounded border">
                </a>
              @else
                <div class="text-xs text-gray-500">—</div>
              @endif
            </td>

            <td class="px-4 py-4 whitespace-nowrap">
              @php
                $status = strtolower($doc->status);
              @endphp

              @if($status === 'approved')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
              @elseif($status === 'rejected')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                @if($doc->rejection_reason)
                  <div class="text-xs text-gray-500 mt-1">Reason: {{ Str::limit($doc->rejection_reason, 60) }}</div>
                @endif
              @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
              @endif
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-right text-sm">
              <div class="flex items-center justify-end space-x-2">
                <form action="{{ route('admin.documents.approve', $doc->id) }}" method="POST" class="inline">
                  @csrf
                  @method('PUT')
                  <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md border border-transparent text-sm font-medium bg-green-600 text-white hover:bg-green-700">
                    Approve
                  </button>
                </form>

                <button type="button" 
                        class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium border border-gray-300 bg-white hover:bg-gray-50"
                        onclick="openRejectModal({{ $doc->id }}, '{{ addslashes($doc->user->name ?? 'User') }}')">
                  Reject
                </button>

                <a href="{{ route('admin.documents.show', $doc->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium border border-gray-300 bg-white hover:bg-gray-50">
                  View
                </a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
              No documents found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-4">
    @if(method_exists($documents, 'links'))
      {{ $documents->links() }}
    @endif
  </div>
</div>

{{-- Rejection modal (simple) --}}
<div id="reject-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
  <div class="bg-white rounded-lg w-full max-w-md p-6">
    <h3 class="text-lg font-medium text-gray-800 mb-4">Reject Document</h3>

    <form id="reject-form" method="POST" action="">
      @csrf
      @method('PUT')

      <input type="hidden" name="document_id" id="reject-document-id">

      <label class="block text-sm font-medium text-gray-700">Reason</label>
      <textarea name="rejection_reason" id="rejection_reason" rows="4" class="mt-1 block w-full border rounded-md p-2 text-sm" placeholder="Enter reason for rejection" required></textarea>

      <div class="mt-4 flex justify-end space-x-2">
        <button type="button" class="px-4 py-2 rounded-md bg-gray-200" onclick="closeRejectModal()">Cancel</button>
        <button type="submit" class="px-4 py-2 rounded-md bg-red-600 text-white">Reject</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openRejectModal(id, userName) {
    const modal = document.getElementById('reject-modal');
    const form = document.getElementById('reject-form');
    const docInput = document.getElementById('reject-document-id');

    // set action url - adjust route name if different
    form.action = '/admin/documents/' + id + '/reject';
    docInput.value = id;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeRejectModal() {
    const modal = document.getElementById('reject-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  // close modal when clicking outside content
  document.getElementById('reject-modal')?.addEventListener('click', function(e) {
    if (e.target.id === 'reject-modal') closeRejectModal();
  });
</script>
@endpush

@endsection
