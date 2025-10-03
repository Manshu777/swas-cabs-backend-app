{{-- resources/views/components/table.blade.php --}}
@props([
    'data' => [],
    'headers' => [],
    'caption' => null,
    'footer' => null,
])

@php
    // Ensure data and headers are arrays
    $data = is_array($data) ? $data : [];
    $headers = is_array($headers) ? $headers : [];
    $totalRides = count($data);
    $caption = (string) $caption;
    // Sanitize headers to ensure all are strings
    $headers = array_map(fn($header) => (string) $header, $headers);
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    {{-- Filter Section --}}
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center space-x-2 w-full sm:w-auto">
            <label for="table-search" class="text-sm font-medium text-gray-700 sr-only sm:not-sr-only">Filter:</label>
            <div class="relative w-full sm:w-64">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input 
                    type="text" 
                    id="table-search" 
                    placeholder="Search rides, customers, or drivers..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                    value=""
                    onkeyup="filterTable(this.value)"
                />
            </div>
        </div>
        <div class="text-sm text-gray-500 font-medium" id="ride-counter">
            Showing {{ $totalRides }} of {{ $totalRides }} Bookings
        </div>
    </div>

    {{-- Table Container --}}
    <div class="relative w-full overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                {{-- Table Header --}}
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 sticky top-0 z-10">
                    <tr>
                        @foreach ($headers as $header)
                            <th class="h-12 px-4 text-left align-middle font-semibold text-gray-700 uppercase tracking-wide text-xs">
                                {{ (string) $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody class="[&_tr:last-child]:border-0" id="table-body">
                    @forelse ($data as $index => $row)
                        @php
                            // Deep validation and string casting for each field to prevent trim() errors
                            $row = is_array($row) ? $row : (array) $row; // Ensure row is array
                            
                            $rideId = (string) ($row['id'] ?? 'N/A');
                            $customerData = (array) ($row['customer'] ?? []);
                            $customerName = (string) ($customerData['name'] ?? 'Unknown');
                            $customerInitial = substr($customerName, 0, 1);
                            $driverData = (array) ($row['driver'] ?? []);
                            $driverName = (string) ($driverData['name'] ?? 'N/A');
                            $routeData = (array) ($row['route'] ?? []);
                            $routeFrom = (string) ($routeData['from'] ?? 'N/A');
                            $routeTo = (string) ($routeData['to'] ?? 'N/A');
                            $statusData = (array) ($row['status'] ?? []);
                            $statusLabel = ucfirst((string) ($statusData['label'] ?? 'unknown'));
                            $statusColor = strtolower((string) ($statusData['color'] ?? 'gray'));
                            $rideType = ucfirst((string) ($row['type'] ?? 'unknown'));
                            $rideTime = (string) ($row['time'] ?? 'N/A');
                            $rideFare = (float) ($row['fare'] ?? 0.00);
                            $isEvenRow = ($index % 2 === 0);
                        @endphp
                        <tr class="border-b border-gray-100 transition-all duration-200 hover:bg-blue-50/50 {{ $isEvenRow ? 'bg-gray-50/50' : '' }}">
                            {{-- Action Column --}}
                            <td class="p-4 align-middle whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <button type="button" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-lg transition-colors" title="Delete">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>

                            {{-- Ride ID --}}
                            <td class="p-4 align-middle whitespace-nowrap">
                                <span class="text-gray-900 font-semibold bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">#{{ $rideId }}</span>
                            </td>

                            {{-- Customer --}}
                            <td class="p-4 align-middle">
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-sm">
                                            <span class="text-sm font-semibold text-white">{{ $customerInitial }}</span>
                                        </div>
                                        <span class="absolute -bottom-1 -right-1 block h-3 w-3 rounded-full bg-green-400 ring-2 ring-white"></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $customerName }}</p>
                                        <p class="text-xs text-gray-500">Customer</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Driver --}}
                            <td class="p-4 align-middle whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $driverName }}</span>
                                </div>
                            </td>

                            {{-- Route --}}
                            <td class="p-4 align-middle">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $routeFrom }}</span>
                                    <span class="text-xs text-gray-500">→ {{ $routeTo }}</span>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="p-4 align-middle whitespace-nowrap">
                                @php
                                    $statusClass = match($statusColor) {
                                        'completed' => 'bg-green-100 text-green-800 border-green-200',
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                        default => 'bg-gray-100 text-gray-800 border-gray-200'
                                    };
                                    $statusIcon = match($statusColor) {
                                        'completed' => '<svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>',
                                        'pending' => '<svg class="h-3 w-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>',
                                        'cancelled' => '<svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y2="9" x2="15" y2="15"></line></svg>',
                                        default => ''
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }} shadow-sm">
                                    {!! $statusIcon !!}
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            {{-- Type --}}
                            <td class="p-4 align-middle whitespace-nowrap">
                                @php
                                    $typeClass = $rideType === 'One-way' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800';
                                    $typeIcon = $rideType === 'One-way' ? '<svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>' : '<svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l9.91-1.01z"></path></svg>';
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $typeClass }}">
                                    {!! $typeIcon !!}
                                    {{ $rideType }}
                                </span>
                            </td>

                            {{-- Time --}}
                            <td class="p-4 align-middle whitespace-nowrap text-gray-600">
                                <div class="flex items-center space-x-1">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ $rideTime }}</span>
                                </div>
                            </td>

                            {{-- Fare --}}
                            <td class="p-4 align-middle whitespace-nowrap font-semibold text-gray-900">
                                <div class="flex items-center space-x-1">
                                    <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>₹{{ number_format($rideFare, 2) }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="{{ count($headers) }}" class="p-12 text-center">
                                <div class="flex flex-col items-center space-y-4">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">No bookings found</h3>
                                        <p class="text-sm text-gray-500 mt-1">Try adjusting your search or filters.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Table Footer (optional) --}}
                @if($footer)
                    <tfoot class="border-t border-gray-200 bg-gray-50">
                        <tr>
                            <td colspan="{{ count($headers) }}" class="p-4 text-right font-semibold text-gray-900">
                                {{ $footer }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Table Caption (optional) --}}
    @if($caption)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-500 italic">{{ $caption }}</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function filterTable(searchValue) {
        const rows = document.querySelectorAll('#table-body tr:not(#empty-row)');
        const emptyRow = document.getElementById('empty-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase().trim();
            const matches = text.includes(searchValue.toLowerCase().trim());
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        // Handle empty state visibility
        if (emptyRow) {
            const showEmpty = (visibleCount === 0 && searchValue.trim() !== '') || (searchValue.trim() === '' && {{ $totalRides }} === 0);
            emptyRow.style.display = showEmpty ? '' : 'none';
        }

        // Update counter
        const counter = document.getElementById('ride-counter');
        if (counter) {
            const total = {{ $totalRides }};
            counter.textContent = `Showing ${visibleCount} of ${total} Bookings`;
        }
    }

    // Add smooth hover transitions and focus states
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('#table-body tr');
        rows.forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.transform = 'translateY(-1px)';
                row.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
            });
            row.addEventListener('mouseleave', () => {
                row.style.transform = 'translateY(0)';
                row.style.boxShadow = 'none';
            });
        });
    });
</script>
@endpush