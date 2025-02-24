<!doctype html>
<html>

<x-head></x-head>

<body>
    <div class="relative flex flex-col w-full max-w-[640px] min-h-screen gap-5 mx-auto bg-[#F5F5F0]">
        <div id="top-bar" class="flex justify-between items-center px-4 mt-[20px]">
            <a href="{{ route('front.details', $handphone->slug) }}"><img
                    src="{{ asset('assets/images/icons/back.svg') }}" class="w-10 h-10" alt="icon">
            </a>
            <p class="font-bold text-2xl leading-[27px]">Booking</p>
            <div class="dummy-btn w-10"></div>
        </div>
        @livewire('order-form', ['handphone' => $handphone, 'orderData' => $orderData])
    </div>
    {{-- <script src="{{ asset('js/booking.js') }}"></script> --}}
</body>

</html>
