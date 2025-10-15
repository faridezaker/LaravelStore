<x-mail::message>
    # سلام {{ $user->name ?? 'کاربر عزیز' }}

    برای فعال‌سازی حساب کاربری خود، لطفاً روی دکمه زیر کلیک کنید 👇

    <x-mail::button :url="$verificationUrl" color="success">تأیید حساب کاربری</x-mail::button>

    اگر این درخواست از طرف شما نبوده، نیازی به انجام کاری نیست.

    با احترام،
    {{ config('app.name') }}
</x-mail::message>
