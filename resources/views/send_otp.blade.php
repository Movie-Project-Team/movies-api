@component('mail::message')
# @lang('Mã OTP của bạn')

@lang('Sử dụng mã OTP sau để xác minh tài khoản của bạn trên :siteName.', ['siteName' => config('setting.site.name')])

@component('mail::panel')
@lang('Mã OTP:') **{{ $code }}**
@endcomponent

@lang('Mã OTP này có hiệu lực trong vòng :time giờ kể từ bây giờ.', ['time' => 1])  
@lang('Vui lòng không chia sẻ mã này với bất kỳ ai.')<br>

@lang('Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.')<br>

Trân trọng,<br>
{{ config('setting.site.name') }}
@endcomponent
