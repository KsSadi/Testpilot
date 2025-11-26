@extends('layouts.backend.master')

@section('title', 'Database Backups')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Page Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-database text-cyan-600 mr-3"></i>
                Database Backups
            </h1>
            <p class="text-gray-600 mt-1">Manage and download your database backups</p>
        </div>
        <div class="flex space-x-3">
            <form action="{{ route('backup.run') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<i class=\'fas fa-spinner fa-spin mr-2\'></i>Creating...'; return true;">
                @csrf
                <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-xl font-medium hover:from-green-600 hover:to-green-700 transition shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus-circle mr-2"></i> Create Backup Now
                </button>
            </form>
            <a href="{{ route('settings.index', ['tab' => 'backup']) }}" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-medium hover:bg-gray-200 transition">
                <i class="fas fa-cog mr-2"></i> Settings
            </a>
        </div>
    </div>

    {{-- Backup Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Total Backups</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ count($backups) }}</h3>
                </div>
                <div class="bg-blue-50 rounded-full p-4">
                    <i class="fas fa-database text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Latest Backup</p>
                    <h3 class="text-xl font-bold text-gray-900">
                        {{ count($backups) > 0 ? \Carbon\Carbon::parse($backups[0]['date'])->diffForHumans() : 'No backups' }}
                    </h3>
                </div>
                <div class="bg-green-50 rounded-full p-4">
                    <i class="fas fa-clock text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Total Size</p>
                    <h3 class="text-2xl font-bold text-gray-900">
                        @php
                            $totalSize = array_sum(array_map(function($b) {
                                return (float) preg_replace('/[^0-9.]/', '', $b['size']) * 
                                    (str_contains($b['size'], 'MB') ? 1024 : 
                                    (str_contains($b['size'], 'GB') ? 1024*1024 : 1));
                            }, $backups));
                        @endphp
                        {{ $totalSize > 1024 ? round($totalSize/1024, 2) . ' MB' : round($totalSize, 2) . ' KB' }}
                    </h3>
                </div>
                <div class="bg-purple-50 rounded-full p-4">
                    <i class="fas fa-hdd text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Auto Backup</p>
                    <h3 class="text-xl font-bold">
                        @if(setting('enable_auto_backup', false))
                            <span class="text-green-600 flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>Enabled
                            </span>
                        @else
                            <span class="text-gray-400 flex items-center">
                                <i class="fas fa-times-circle mr-2"></i>Disabled
                            </span>
                        @endif
                    </h3>
                </div>
                <div class="bg-cyan-50 rounded-full p-4">
                    <i class="fas fa-robot text-2xl text-cyan-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <form action="{{ route('backup.cleanup') }}" method="POST" onsubmit="return confirm('Remove old backups based on retention policy?');">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-3 bg-blue-50 border-2 border-blue-200 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                    <i class="fas fa-broom mr-2"></i>
                    <span class="font-medium">Clean Old Backups</span>
                </button>
            </form>
            <a href="{{ route('settings.index', ['tab' => 'backup']) }}" class="w-full flex items-center justify-center px-4 py-3 bg-purple-50 border-2 border-purple-200 text-purple-700 rounded-lg hover:bg-purple-100 transition">
                <i class="fas fa-cog mr-2"></i>
                <span class="font-medium">Configure Settings</span>
            </a>
            <button onclick="window.location.reload()" class="w-full flex items-center justify-center px-4 py-3 bg-gray-50 border-2 border-gray-200 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-sync-alt mr-2"></i>
                <span class="font-medium">Refresh List</span>
            </button>
        </div>
    </div>

    {{-- Backups Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Available Backups ({{ count($backups) }})</h3>
            <div class="flex items-center space-x-3">
                <input type="text" id="searchBackup" placeholder="Search backups..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 text-sm" onkeyup="filterBackups()">
                <select id="filterDate" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 text-sm" onchange="filterBackups()">
                    <option value="">All Dates</option>
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
        </div>
        
        @if(count($backups) === 0)
            <div class="text-center py-16">
                <div class="bg-gray-50 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-5xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Backups Found</h3>
                <p class="text-gray-600 mb-6">Create your first backup to get started</p>
                <form action="{{ route('backup.run') }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-xl font-medium hover:from-green-600 hover:to-green-700 transition">
                        <i class="fas fa-plus-circle mr-2"></i> Create First Backup
                    </button>
                </form>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Backup File</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="backupTableBody">
                        @foreach($backups as $backup)
                        <tr class="hover:bg-gray-50 transition backup-row" data-date="{{ $backup['date'] }}" data-name="{{ $backup['name'] }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-cyan-50 rounded-lg">
                                        <i class="fas fa-file-archive text-cyan-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $backup['name'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $backup['size'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2 text-gray-400"></i>
                                    <div>
                                        <div>{{ $backup['date'] }}</div>
                                        <div class="text-xs text-gray-400">({{ \Carbon\Carbon::parse($backup['date'])->diffForHumans() }})</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('backup.download', basename($backup['name'])) }}" 
                                   class="inline-flex items-center px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition mr-2"
                                   title="Download Backup">
                                    <i class="fas fa-download mr-1"></i> Download
                                </a>
                                <form action="{{ route('backup.delete', basename($backup['name'])) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition" 
                                            onclick="return confirm('Are you sure you want to delete this backup?\n\nFile: {{ $backup['name'] }}')"
                                            title="Delete Backup">
                                        <i class="fas fa-trash mr-1"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Info Box --}}
    <div class="bg-cyan-50 border-l-4 border-cyan-500 p-4 rounded-lg mt-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-cyan-600 mt-0.5 mr-3"></i>
            <div class="text-sm text-cyan-800">
                <p class="font-medium mb-1">Backup Information</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong>Storage Location:</strong> <code class="bg-cyan-100 px-2 py-1 rounded">storage/app/Laravel</code></li>
                    <li><strong>Retention Period:</strong> {{ setting('backup_retention_days', 30) }} days</li>
                    <li><strong>Backup Frequency:</strong> {{ ucfirst(setting('backup_frequency', 'daily')) }}</li>
                    <li><strong>Storage Type:</strong> {{ ucfirst(setting('backup_storage', 'local')) }}</li>
                </ul>
                <p class="mt-2 text-xs">💡 <strong>Tip:</strong> Always download and store critical backups in a secure off-site location.</p>
            </div>
        </div>
    </div>
</div>

<script>
function filterBackups() {
    const searchTerm = document.getElementById('searchBackup').value.toLowerCase();
    const dateFilter = document.getElementById('filterDate').value;
    const rows = document.querySelectorAll('.backup-row');
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name').toLowerCase();
        const date = new Date(row.getAttribute('data-date'));
        const now = new Date();
        
        let showDate = true;
        if (dateFilter === 'today') {
            showDate = date.toDateString() === now.toDateString();
        } else if (dateFilter === 'yesterday') {
            const yesterday = new Date(now);
            yesterday.setDate(yesterday.getDate() - 1);
            showDate = date.toDateString() === yesterday.toDateString();
        } else if (dateFilter === 'week') {
            const weekAgo = new Date(now);
            weekAgo.setDate(weekAgo.getDate() - 7);
            showDate = date >= weekAgo;
        } else if (dateFilter === 'month') {
            const monthAgo = new Date(now);
            monthAgo.setMonth(monthAgo.getMonth() - 1);
            showDate = date >= monthAgo;
        }
        
        const matchesSearch = name.includes(searchTerm);
        row.style.display = (matchesSearch && showDate) ? '' : 'none';
    });
}
</script>
@endsection
