<?php $__env->startSection('title', 'Dashboard Overview'); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Dashboard Overview</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">
                Welcome back, <?php echo e(Auth::user()->name ?? 'Guest'); ?>! Here's your business summary.
            </p>
        </div>
        <div class="action-buttons flex items-center space-x-2">
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <i class="fas fa-download mr-2"></i>Export
            </button>
            <button class="px-4 py-2 primary-color text-white rounded-lg hover:shadow-lg transition text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>Add New
            </button>
        </div>
    </div>

    
    <div class="stats-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <div class="bg-white rounded-xl p-4 stat-card border border-gray-100 shadow-sm animate-slide-in">
            <div class="flex items-center justify-between mb-3">
                <div class="primary-color rounded-lg p-2.5 gradient-hover">
                    <i class="fas fa-dollar-sign text-white text-lg"></i>
                </div>
                <span class="bg-green-50 text-green-600 text-xs font-semibold px-2 py-1 rounded-md">
                    <i class="fas fa-arrow-up text-[10px]"></i> 12.5%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-medium mb-1">Total Revenue</p>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">$<?php echo e(number_format(54239, 2)); ?></h3>
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">Goal: $60,000</span>
                <span class="text-cyan-600 font-medium">90%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1 mt-2">
                <div class="primary-color h-1 rounded-full transition-all duration-500" style="width: 90%"></div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl p-4 stat-card border border-gray-100 shadow-sm animate-slide-in" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-gradient-to-br from-emerald-400 to-green-600 rounded-lg p-2.5 gradient-hover">
                    <i class="fas fa-users text-white text-lg"></i>
                </div>
                <span class="bg-green-50 text-green-600 text-xs font-semibold px-2 py-1 rounded-md">
                    <i class="fas fa-arrow-up text-[10px]"></i> 8.2%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-medium mb-1">New Customers</p>
            <h3 class="text-2xl font-bold text-gray-800 mb-2"><?php echo e(number_format(2842)); ?></h3>
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">This month</span>
                <span class="text-green-600 font-medium">+234</span>
            </div>
        </div>

        
        <div class="bg-white rounded-xl p-4 stat-card border border-gray-100 shadow-sm animate-slide-in" style="animation-delay: 0.2s;">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg p-2.5 gradient-hover">
                    <i class="fas fa-shopping-bag text-white text-lg"></i>
                </div>
                <span class="bg-red-50 text-red-600 text-xs font-semibold px-2 py-1 rounded-md">
                    <i class="fas fa-arrow-down text-[10px]"></i> 2.1%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-medium mb-1">Total Orders</p>
            <h3 class="text-2xl font-bold text-gray-800 mb-2"><?php echo e(number_format(1482)); ?></h3>
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">Pending: 48</span>
                <span class="text-orange-600 font-medium">3.2%</span>
            </div>
        </div>

        
        <div class="bg-white rounded-xl p-4 stat-card border border-gray-100 shadow-sm animate-slide-in" style="animation-delay: 0.3s;">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-2.5 gradient-hover">
                    <i class="fas fa-chart-line text-white text-lg"></i>
                </div>
                <span class="bg-green-50 text-green-600 text-xs font-semibold px-2 py-1 rounded-md">
                    <i class="fas fa-arrow-up text-[10px]"></i> 5.3%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-medium mb-1">Conversion Rate</p>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">68.4%</h3>
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">Avg. time: 2.4m</span>
                <span class="text-purple-600 font-medium">Good</span>
            </div>
        </div>
    </div>

    
    <div class="bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-xl p-4 md:p-5 mb-6 shadow-xl border border-cyan-400">
        <div class="quick-actions-content flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="bg-white bg-opacity-20 rounded-xl p-3 backdrop-blur-sm">
                    <i class="fas fa-bolt text-white text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-base md:text-lg">Quick Actions</h3>
                    <p class="text-cyan-100 text-xs md:text-sm">Perform common tasks instantly</p>
                </div>
            </div>
            <div class="quick-actions-buttons flex items-center gap-2 flex-wrap">
                <button class="bg-white text-cyan-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-cyan-50 hover:shadow-lg transition-all">
                    <i class="fas fa-plus mr-2"></i>New Order
                </button>
                <button class="bg-white bg-opacity-20 backdrop-blur-sm text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-white hover:bg-opacity-30 transition-all">
                    <i class="fas fa-user-plus mr-2"></i>Add Customer
                </button>
                <button class="bg-white bg-opacity-20 backdrop-blur-sm text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-white hover:bg-opacity-30 transition-all">
                    <i class="fas fa-file-invoice mr-2"></i>Create Invoice
                </button>
            </div>
        </div>
    </div>

    
    <div class="chart-grid grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        
        <div class="lg:col-span-2 bg-white rounded-xl p-4 md:p-5 border border-gray-100 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-3">
                <div>
                    <h3 class="text-base md:text-lg font-bold text-gray-800">Revenue Analytics</h3>
                    <p class="text-gray-500 text-xs mt-0.5">Monthly performance</p>
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-1.5 primary-color text-white text-xs font-medium rounded-lg">
                        Monthly
                    </button>
                    <button class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200">
                        Yearly
                    </button>
                </div>
            </div>
            <div class="h-56 flex items-end justify-between space-x-2">
                <?php
                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    $heights = [45, 60, 75, 55, 80, 65, 90, 70, 85, 60, 95, 88];
                ?>
                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex flex-col items-center flex-1 group">
                        <div class="w-full primary-color rounded-t-lg hover:opacity-90 cursor-pointer transition" style="height: <?php echo e($heights[$index]); ?>%"></div>
                        <span class="text-xs text-gray-600 mt-2 font-medium"><?php echo e($month); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="bg-white rounded-xl p-4 md:p-5 border border-gray-100 shadow-sm">
            <h3 class="text-base md:text-lg font-bold text-gray-800 mb-4">Top Products</h3>
            <div class="space-y-4">
                <?php
                    $products = [
                        ['name' => 'Wireless Headphones', 'sales' => 1234, 'percentage' => 85, 'color' => 'cyan'],
                        ['name' => 'Smart Watch Pro', 'sales' => 987, 'percentage' => 70, 'color' => 'green'],
                        ['name' => 'Laptop Stand', 'sales' => 756, 'percentage' => 60, 'color' => 'orange'],
                        ['name' => 'USB-C Hub', 'sales' => 543, 'percentage' => 45, 'color' => 'purple'],
                    ];
                ?>
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-800"><?php echo e($product['name']); ?></span>
                            <span class="text-xs text-gray-500"><?php echo e($product['sales']); ?> sales</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-<?php echo e($product['color']); ?>-500 h-2 rounded-full" style="width: <?php echo e($product['percentage']); ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base md:text-lg font-bold text-gray-800">Recent Orders</h3>
                    <p class="text-gray-500 text-xs mt-0.5">Latest customer orders</p>
                </div>
                <button class="text-cyan-600 hover:text-cyan-700 font-medium text-sm">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-gray-600 uppercase">Order ID</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-gray-600 uppercase">Customer</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                        $orders = [
                            ['id' => '2847', 'customer' => 'Sarah Johnson', 'email' => 'sarah.j@email.com', 'date' => 'Nov 3, 2025', 'amount' => 485.50, 'status' => 'Pending', 'status_color' => 'yellow'],
                            ['id' => '2846', 'customer' => 'Michael Chen', 'email' => 'michael.c@email.com', 'date' => 'Nov 3, 2025', 'amount' => 268.00, 'status' => 'Completed', 'status_color' => 'green'],
                            ['id' => '2845', 'customer' => 'Emily Rodriguez', 'email' => 'emily.r@email.com', 'date' => 'Nov 2, 2025', 'amount' => 320.75, 'status' => 'Processing', 'status_color' => 'blue'],
                            ['id' => '2844', 'customer' => 'David Park', 'email' => 'd.park@email.com', 'date' => 'Nov 1, 2025', 'amount' => 156.00, 'status' => 'Completed', 'status_color' => 'green'],
                            ['id' => '2843', 'customer' => 'Lisa Anderson', 'email' => 'lisa.a@email.com', 'date' => 'Nov 1, 2025', 'amount' => 275.25, 'status' => 'Cancelled', 'status_color' => 'red'],
                        ];
                    ?>
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-3">
                                <span class="text-sm font-semibold text-gray-800">#<?php echo e($order['id']); ?></span>
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($order['customer'])); ?>&background=random&color=fff&font-size=0.4" 
                                         alt="<?php echo e($order['customer']); ?>" 
                                         class="w-8 h-8 rounded-lg mr-2">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800"><?php echo e($order['customer']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo e($order['email']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-sm text-gray-600"><?php echo e($order['date']); ?></td>
                            <td class="py-3 px-3 text-sm font-semibold text-gray-800">$<?php echo e(number_format($order['amount'], 2)); ?></td>
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md 
                                    <?php if($order['status_color'] == 'green'): ?> bg-green-50 text-green-700
                                    <?php elseif($order['status_color'] == 'yellow'): ?> bg-yellow-50 text-yellow-700
                                    <?php elseif($order['status_color'] == 'blue'): ?> bg-blue-50 text-blue-700
                                    <?php elseif($order['status_color'] == 'red'): ?> bg-red-50 text-red-700
                                    <?php endif; ?>">
                                    <?php if($order['status'] == 'Completed'): ?>
                                        <i class="fas fa-check-circle mr-1 text-[10px]"></i>
                                    <?php elseif($order['status'] == 'Pending'): ?>
                                        <i class="fas fa-clock mr-1 text-[10px]"></i>
                                    <?php elseif($order['status'] == 'Processing'): ?>
                                        <i class="fas fa-sync mr-1 text-[10px]"></i>
                                    <?php elseif($order['status'] == 'Cancelled'): ?>
                                        <i class="fas fa-times-circle mr-1 text-[10px]"></i>
                                    <?php endif; ?>
                                    <?php echo e($order['status']); ?>

                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <button class="text-cyan-600 hover:text-cyan-700 font-medium text-xs">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Any page-specific JavaScript here
    console.log('Dashboard page loaded');
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.backend.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Arpa Nihan_personal\code_study\Testpilot\app\Modules/Dashboard/resources/views/index.blade.php ENDPATH**/ ?>