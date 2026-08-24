<x-mail::message>
# Service Complete

Hello **{{ $customerName }}**,

{{ $messageBody }}

**Bike:** {{ $bikeName }}

@if ($shopPhone)
**Contact:** {{ $shopPhone }}
@endif

@if ($shopAddress)
**Address:** {{ $shopAddress }}
@endif

Thanks,<br>
{{ $shopName }}
</x-mail::message>
