<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Wallet Card -->
            <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">
                                <i class="fas fa-wallet ml-2"></i>
                                رصيد المحفظة
                            </h3>
                            <p class="text-4xl font-bold">
                                ${{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Orders Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">الطلبات</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">
                                    {{ auth()->user()->orders()->count() }}
                                </p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Designs Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">التصاميم</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">
                                    {{ auth()->user()->designs()->count() }}
                                </p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-palette text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">المعاملات</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">
                                    {{ auth()->user()->transactions()->count() }}
                                </p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-exchange-alt text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">مرحباً بك، {{ auth()->user()->name }}!</h3>
                    <p class="text-gray-600">
                        {{ __("You're logged in!") }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
