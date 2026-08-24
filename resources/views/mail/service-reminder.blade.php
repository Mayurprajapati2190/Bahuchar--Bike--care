<x-mail::message>
# Service Reminder

Hello **{{ $customerName }}**,

{{ $messageBody }}

**Bike:** {{ $bikeName }}

@if ($shopPhone)
**Contact:** {{ $shopPhone }}
@endif

Thanks,<br>
{{ $shopName }}
</x-mail::message>
