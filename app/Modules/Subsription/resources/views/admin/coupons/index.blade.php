@extends('layouts.backend.master')

@section('title', 'Discount Coupons Management')

@section('content')
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Discount Coupons</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">
                Create and manage subscription discount coupons
            </p>
        </div>
        <div>
            <a href="{{ route('admin.subscriptions.coupons.create') }}" class="px-4 py-2 primary-color text-white rounded-lg hover:shadow-lg transition text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>Create Coupon
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-mono font-bold text-blue-600">{{ $coupon->code }}</div>
                                @if($coupon->applicable_plans)
                                    <div class="text-xs text-gray-500 mt-1">
                                        For: {{ implode(', ', json_decode($coupon->applicable_plans)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    @if($coupon->discount_type === 'percentage')
                                        {{ $coupon->discount_value }}% OFF
                                    @else
                                        ${{ number_format($coupon->discount_value, 2) }} OFF
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($coupon->valid_from)
                                    <div>From: {{ $coupon->valid_from->format('M d, Y') }}</div>
                                @endif
                                @if($coupon->valid_until)
                                    <div>Until: {{ $coupon->valid_until->format('M d, Y') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $coupon->redemptions()->count() }}
                                @if($coupon->max_uses)
                                    / {{ $coupon->max_uses }}
                                @else
                                    / ∞
                                @endif
                                uses
                                @if($coupon->max_uses_per_user)
                                    <div class="text-xs">(max {{ $coupon->max_uses_per_user }}/user)</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.subscriptions.coupons.toggle', $coupon) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 text-xs font-semibold rounded-full {{ $coupon->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.subscriptions.coupons.edit', $coupon) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.subscriptions.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Delete this coupon?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No coupons found. <a href="{{ route('admin.subscriptions.coupons.create') }}" class="text-blue-600 hover:underline">Create your first coupon</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
