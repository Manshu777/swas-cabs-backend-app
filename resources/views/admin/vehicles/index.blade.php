@extends('layouts.admin')

@section('title', 'Driver Vehicles')

@section('content')
<div class="container mx-auto px-4 py-6">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Driver Vehicles</h1>
    <div class="text-sm text-gray-600">Total: {{ $vehicles->total() ?? $vehicles->count() }}</div>
  </div>

  <div class="bg-white shadow rounded-lg overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver ID</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License Plate</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>

      <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($vehicles as $vehicle)
          <tr>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ $loop->iteration + (($vehicles->currentPage() - 1) * $vehicles->perPage() ?? 0) }}
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
              {{ $vehicle->driver_id }}
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
              {{ $vehicle->brand }}
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
              {{ $vehicle->model }}
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
              {{ $vehicle->license_plate }}
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
              {{ $vehicle->vehicle_type }}
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
              {{ $vehicle->year }}
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
              {{ $vehicle->color ?? '—' }}
            </td>

            <td class="px-4 py-4 whitespace-nowrap">
              @if ($vehicle->status === 'approved')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  Approved
                </span>
              @elseif ($vehicle->status === 'rejected')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                  Rejected
                </span>
                @if ($vehicle->rejection_reason)
                  <div class="text-xs text-gray-500 mt-1">
                    Reason: {{ Str::limit($vehicle->rejection_reason, 60) }}
                  </div>
                @endif
              @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                  Pending
                </span>
              @endif
            </td>

            <td class="px-4 py-4 whitespace-nowrap text-right text-sm">
              <div class="flex items-center justify-end space-x-2">
                <form action="{{ route('admin.vehicles.approve', $vehicle->id) }}" method="POST" class="inline">
                  @csrf
                  @method('PUT')
                  <button type="submit"
                    class="inline-flex items-center px-3 py-1.5 rounded-md border border-transparent text-sm font-medium bg-green-600 text-white hover:bg-green-700">
                    Approve
                  </button>
                </form>

                <button type="button"
                        class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium border border-gray-300 bg-white hover:bg-gray-50"
                        onclick="openRejectModal({{ $vehicle->id }}, '{{ addslashes($vehicle->brand . ' ' . $vehicle->model) }}')">
                  Reject
                </button>

              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="px-6 py-8 text-center text-gray-500">
              No vehicles found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-4">
    @if(method_exists($vehicles, 'links'))
      {{ $vehicles->links() }}
    @endif
  </div>
</div>

{{-- Rejection Modal --}}
<div id="reject-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
  <div class="bg-white rounded-lg w-full max-w-md p-6">
    <h3 class="text-lg font-medium text-gray-800 mb-4">Reject Vehicle</h3>

    <form id="reject-form" method="POST" action="">
      @csrf
      @method('PUT')
      <input type="hidden" name="vehicle_id" id="reject-vehicle-id">

      <label class="block text-sm font-medium text-gray-700">Reason</label>
      <textarea name="rejection_reason" id="rejection_reason" rows="4"
        class="mt-1 block w-full border rounded-md p-2 text-sm"
        placeholder="Enter reason for rejection" required></textarea>

      <div class="mt-4 flex justify-end space-x-2">
        <button type="button" class="px-4 py-2 rounded-md bg-gray-200" onclick="closeRejectModal()">Cancel</button>
        <button type="submit" class="px-4 py-2 rounded-md bg-red-600 text-white">Reject</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openRejectModal(id, vehicleName) {
    const modal = document.getElementById('reject-modal');
    const form = document.getElementById('reject-form');
    const input = document.getElementById('reject-vehicle-id');

    form.action = '/admin/vehicles/' + id + '/reject';
    input.value = id;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeRejectModal() {
    const modal = document.getElementById('reject-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  document.getElementById('reject-modal')?.addEventListener('click', function (e) {
    if (e.target.id === 'reject-modal') closeRejectModal();
  });
</script>
@endpush

@endsection
