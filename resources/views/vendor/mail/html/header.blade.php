<tr>
<td class="header">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img src="{{ config('app.url') }}/img/logo.png" class="logo" alt="{{ $slot }}">
@endif
</td>
</tr>
