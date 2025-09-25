@extends('client.layout')

@section('title', 'Danh sách vé của tôi')

@push('styles')
<style>
    .ticket-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-left: 4px solid #6366f1;
        transition: all 0.3s ease;
    }
    .ticket-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .status-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
    }
    .movie-poster {
        width: 80px;
        height: 120px;
        object-fit: cover;
        border-radius: 0.5rem;
    }
    .info-label {
        color: #9ca3af;
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
    }
    .info-value {
        color: #f3f4f6;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-white mb-3">Danh sách vé của tôi</h1>
            <div class="w-20 h-1 bg-indigo-600 mx-auto rounded-full"></div>
            <p class="mt-4 text-gray-400 max-w-2xl mx-auto">Xem lịch sử và quản lý các vé xem phim của bạn một cách dễ dàng</p>
        </div>

        @if($tickets->isEmpty())
            <div class="bg-gray-800 rounded-xl p-10 text-center max-w-2xl mx-auto">
                <div class="bg-gray-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-10 w-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2m5-11a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V9z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Bạn chưa có vé nào</h3>
                <p class="text-gray-400 mb-6">Hãy đặt vé để bắt đầu trải nghiệm xem phim tuyệt vời</p>
                <a href="{{ route('movies.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Đặt vé ngay
                </a>
            </div>
        @else
            <div class="space-y-5">
                @foreach($tickets as $ticket)
                <a href="{{ route('me.tickets.show', $ticket) }}" class="block group">
                    <div class="ticket-card rounded-xl p-5 relative overflow-hidden">
                        <!-- Status Badge -->
                        <span class="status-badge {{ $ticket->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $ticket->status === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán' }}
                        </span>
                        
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Movie Poster -->
                            <div class="flex-shrink-0">
                                <img src="{{ $ticket->showtime->movie->poster_url ?? 'https://via.placeholder.com/80x120' }}" 
                                     alt="{{ $ticket->showtime->movie->title }}" 
                                     class="movie-poster shadow-lg">
                            </div>
                            
                            <!-- Ticket Details -->
                            <div class="flex-1">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-3">
                                    <h3 class="text-xl font-bold text-white mb-2 md:mb-0">
                                        {{ $ticket->showtime->movie->title }}
                                        <span class="text-gray-400 text-sm font-normal">({{ $ticket->showtime->movie->duration }} phút)</span>
                                    </h3>
                                    <div class="text-indigo-400 font-mono text-sm bg-gray-700 px-3 py-1 rounded-full inline-flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2m5-11a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V9z" />
                                        </svg>
                                        {{ $ticket->code }}
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                                    <div>
                                        <div class="info-label">Ngày chiếu</div>
                                        <div class="info-value flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $ticket->showtime->start_time->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="info-label">Giờ chiếu</div>
                                        <div class="info-value flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $ticket->showtime->start_time->format('H:i') }} - {{ $ticket->showtime->end_time->format('H:i') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="info-label">Phòng & Ghế</div>
                                        <div class="info-value flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            Phòng {{ $ticket->showtime->room->name }}, Ghế {{ $ticket->seat->seat_number }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="info-label">Tổng tiền</div>
                                        <div class="info-value flex items-center text-yellow-400 font-semibold">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ number_format($ticket->price) }} VNĐ
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-700">
                                    <div class="flex justify-end">
                                        <span class="inline-flex items-center text-sm text-indigo-400 group-hover:text-indigo-300 transition-colors">
                                            Xem chi tiết
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
                
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $tickets->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
