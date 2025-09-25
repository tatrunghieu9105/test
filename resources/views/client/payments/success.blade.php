@extends('client.layout')

@section('title', 'Đặt vé thành công')

@push('styles')
<style>
    .success-animation {
        animation: bounceIn 0.8s ease-in-out;
    }
    @keyframes bounceIn {
        0% { transform: scale(0.9); opacity: 0; }
        50% { transform: scale(1.03); }
        100% { transform: scale(1); opacity: 1; }
    }
    .ticket-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-left: 4px solid #6366f1;
        transition: all 0.3s ease;
    }
    .ticket-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .qr-container {
        background: white;
        padding: 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        display: inline-block;
        position: relative;
        overflow: hidden;
    }
    .qr-container::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            45deg,
            transparent 0%,
            rgba(255, 255, 255, 0.1) 50%,
            transparent 100%
        );
        transform: rotate(45deg);
        animation: shine 3s infinite;
    }
    @keyframes shine {
        0% { transform: translateX(-100%) rotate(45deg); }
        100% { transform: translateX(100%) rotate(45deg); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Success Alert -->
        @if(session('success'))
        <div class="mb-8 bg-green-500/10 border border-green-500/30 rounded-xl p-4 backdrop-blur-sm success-animation">
            <div class="flex items-center">
                <div class="flex-shrink-0 text-green-400 text-2xl">✓</div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-300">{{ session('success') }}</h3>
                </div>
            </div>
        </div>
        @endif

            <!-- Content -->
            <div class="p-6 md:p-8">
                <!-- Order Summary -->
                <div class="bg-gray-700/50 rounded-xl p-6 mb-8 border border-gray-600/30">
                    <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                        <span class="text-blue-400 mr-2">📋</span>
                        Thông tin đơn hàng
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-400 mb-1">Mã đơn hàng</p>
                            <p class="font-mono text-yellow-400">#{{ $order_code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400 mb-1">Ngày đặt</p>
                            <p class="text-white">{{ now()->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400 mb-1">Phương thức thanh toán</p>
                            <div class="flex items-center">
                                @if($payment_method === 'Tiền mặt')
                                <span class="text-green-500 mr-2">💵</span>
                                @else
                                <span class="text-blue-500 mr-2">💳</span>
                                @endif
                                <span class="font-medium">{{ $payment_method }}</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400 mb-1">Tổng tiền</p>
                            <p class="text-2xl font-bold text-green-400">{{ number_format($amount) }} VNĐ</p>
                        </div>
                    </div>
                </div>

                <!-- Tickets -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                        <span class="text-blue-400 mr-2">🎫</span>
                        Thông tin vé
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach($tickets as $ticket)
                        <div class="ticket-card rounded-xl p-5 relative overflow-hidden">
                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-24 bg-gray-600 rounded-lg overflow-hidden">
                                        @if($ticket->showtime->movie->poster_url)
                                        <img src="{{ $ticket->showtime->movie->poster_url }}" alt="{{ $ticket->showtime->movie->title }}" class="w-full h-full object-cover">
                                        @else
                                        <div class="w-full h-full bg-gray-700 flex items-center justify-center">
                                            📷
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                        <h4 class="text-lg font-bold text-white">{{ $ticket->showtime->movie->title }}</h4>
                                        <span class="text-xs font-mono bg-blue-500 text-white px-2 py-1 rounded-full mt-1 sm:mt-0">
                                            {{ $ticket->code }}
                                        </span>
                                    </div>
                                    
                                    <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <p class="text-gray-400">Ngày chiếu</p>
                                            <p class="text-white">{{ $ticket->showtime->start_time->format('d/m/Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400">Giờ chiếu</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400">Phòng</p>
                                            <p class="text-white">Phòng {{ $ticket->showtime->room->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400">Ghế</p>
                                            <p class="text-white">Ghế {{ $ticket->seat->seat_number }}</p>
                                        </div>
                                        <div class="col-span-2">
                                            <p class="text-gray-400">Giá vé</p>
                                            <p class="text-yellow-400 font-medium">{{ number_format($ticket->price) }} VNĐ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <!-- Actions -->
                <div class="flex flex-col sm:flex-row justify-center gap-4 mt-10">
                    <a href="{{ route('me.orders') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        📋
                        Xem vé của tôi
                    </a>
                    <a href="{{ route('movies.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        🏠
                        Về trang chủ
                    </a>
                </div>

                <!-- Help Section -->
                <div class="mt-12 pt-6 border-t border-gray-700 text-center">
                    <h4 class="text-sm font-medium text-gray-400 mb-2">Cần hỗ trợ?</h4>
                    <div class="flex flex-col sm:flex-row justify-center items-center gap-4 text-sm">
                        <a href="#" class="text-blue-400 hover:text-blue-300 flex items-center">
                            📞
                            1900 1234
                        </a>
                        <span class="hidden sm:inline text-gray-600">•</span>
                        <a href="#" class="text-blue-400 hover:text-blue-300 flex items-center">
                            ✉️
                            support@cinema.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Tạo mã QR từ URL hiện tại
    document.addEventListener('DOMContentLoaded', function() {
        new QRCode(document.getElementById("qrcode"), {
            text: window.location.href,
            width: 160,
            height: 160,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    });
</script>
@endpush
@endsection
