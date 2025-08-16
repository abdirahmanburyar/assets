<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Asset Tag</label>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $record->asset_tag }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Serial Number</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->serial_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $record->category->name ?? 'N/A' }}
                    </span>
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Item Description</label>
                <p class="mt-1 text-sm text-gray-900">{{ $record->item_description }}</p>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Status</label>
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'in_use' => 'bg-green-100 text-green-800',
                        'maintenance' => 'bg-blue-100 text-blue-800',
                        'retired' => 'bg-red-100 text-red-800',
                        'rejected' => 'bg-red-100 text-red-800',
                    ];
                    $statusColor = $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                    {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                </span>
            </div>
        </div>

        <!-- Location Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Region</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->location->region->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Location</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->location->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sub-Location</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->subLocation->name ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Full Location Path</label>
                <p class="mt-1 text-sm text-gray-900">{{ $record->full_location_path }}</p>
            </div>
        </div>

        <!-- Assignment & Ownership -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assignment & Ownership</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Assigned To</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->assignee->name ?? 'Unassigned' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->assignee->email ?? 'N/A' }}</p>
                </div>
                @if($record->assignee?->phone)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->assignee->phone }}</p>
                </div>
                @endif
                @if($record->assignee?->department)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Department</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->assignee->department }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Financial Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Original Value</label>
                    <p class="mt-1 text-sm text-gray-900">${{ number_format($record->original_value, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Value</label>
                    <p class="mt-1 text-sm {{ $record->current_value < $record->original_value ? 'text-red-600' : 'text-green-600' }}">
                        ${{ number_format($record->current_value, 2) }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Depreciation Rate</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->depreciation_rate }}%</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Depreciation Start Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->depreciation_start_date?->format('M d, Y') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Acquisition & Warranty -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Acquisition & Warranty</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Acquisition Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->acquisition_date?->format('M d, Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Funded Source</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->fundedSource->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Warranty Expiry</label>
                    <p class="mt-1 text-sm {{ $record->warranty_expiry && $record->warranty_expiry->isPast() ? 'text-red-600' : 'text-green-600' }}">
                        {{ $record->warranty_expiry?->format('M d, Y') ?? 'N/A' }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Maintenance Interval</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->maintenance_interval_days }} days</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Next Maintenance</label>
                    <p class="mt-1 text-sm {{ $record->next_maintenance_date && $record->next_maintenance_date->isPast() ? 'text-red-600' : 'text-green-600' }}">
                        {{ $record->next_maintenance_date?->format('M d, Y') ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">System Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created By</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->creator->name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created At</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->created_at->format('M d, Y H:i') }}</p>
                </div>
                @if($record->approved_by)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Approved By</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->approver->name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Approved At</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->approved_at->format('M d, Y H:i') ?? 'N/A' }}</p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $record->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        @if($record->notes)
        <!-- Notes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h3>
            <div>
                <p class="text-sm text-gray-900">{{ $record->notes }}</p>
            </div>
        </div>
        @endif

        <!-- Asset Documents -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Asset Documents</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $record->documents->count() }} document(s)
                </span>
            </div>
            
            @if($record->documents->count() > 0)
                <div class="space-y-3">
                    @foreach($record->documents as $document)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <!-- File Type Icon -->
                                    <div class="flex-shrink-0">
                                        @if(str_ends_with(strtolower($document->file_path), '.pdf'))
                                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Document Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $document->file_name }}
                                            </p>
                                            @php
                                                $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                                                $fileType = match($extension) {
                                                    'pdf' => ['PDF', 'bg-red-100 text-red-800'],
                                                    'jpg', 'jpeg', 'png', 'gif' => ['Image', 'bg-blue-100 text-blue-800'],
                                                    default => ['File', 'bg-gray-100 text-gray-800']
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $fileType[1] }}">
                                                {{ $fileType[0] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                                            <span>{{ $document->file_size_formatted }}</span>
                                            <span>•</span>
                                            <span>Uploaded {{ $document->created_at->diffForHumans() }}</span>
                                            @if($document->uploader)
                                                <span>•</span>
                                                <span>by {{ $document->uploader->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    @if(str_ends_with(strtolower($document->file_path), '.pdf') || in_array(strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                        <button 
                                            onclick="openPreviewModal('{{ $document->file_name }}', '{{ $document->download_url }}', '{{ strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION)) }}')"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Preview
                                        </button>
                                    @endif
                                    
                                    <a href="{{ $document->download_url }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No documents uploaded</h3>
                    <p class="mt-1 text-sm text-gray-500">This asset doesn't have any documents yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Document Preview</h3>
                    <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Content -->
                <div class="mt-2">
                    <div id="modalContent" class="min-h-96">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex items-center justify-end mt-4 space-x-3">
                    <button onclick="closePreviewModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Close
                    </button>
                    <a id="modalDownloadBtn" href="#" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Modal -->
    <script>
        function openPreviewModal(fileName, fileUrl, fileType) {
            const modal = document.getElementById('previewModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalContent = document.getElementById('modalContent');
            const modalDownloadBtn = document.getElementById('modalDownloadBtn');
            
            // Set modal title and download button
            modalTitle.textContent = `Preview: ${fileName}`;
            modalDownloadBtn.href = fileUrl;
            
            // Clear previous content
            modalContent.innerHTML = '';
            
            // Load content based on file type
            if (fileType === 'pdf') {
                // PDF Preview
                modalContent.innerHTML = `
                    <div class="w-full h-96">
                        <iframe src="${fileUrl}" class="w-full h-full border border-gray-300 rounded-lg" frameborder="0"></iframe>
                    </div>
                `;
            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
                // Image Preview
                modalContent.innerHTML = `
                    <div class="flex justify-center">
                        <img src="${fileUrl}" alt="${fileName}" class="max-w-full max-h-96 object-contain border border-gray-300 rounded-lg">
                    </div>
                `;
            } else {
                // Unsupported file type
                modalContent.innerHTML = `
                    <div class="text-center py-20">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Preview Not Available</h3>
                        <p class="mt-2 text-sm text-gray-500">This file type cannot be previewed. Please download to view.</p>
                    </div>
                `;
            }
            
            // Show modal
            modal.classList.remove('hidden');
            
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }
        
        function closePreviewModal() {
            const modal = document.getElementById('previewModal');
            modal.classList.add('hidden');
            
            // Restore body scroll
            document.body.style.overflow = 'auto';
        }
        
        // Close modal when clicking outside
        document.getElementById('previewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePreviewModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePreviewModal();
            }
        });
    </script>
</x-filament-panels::page>
