@component('mail::message')
# @lang('Xác thực tài khoản thành công')

@lang('Xin chúc mừng! Tài khoản của bạn trên :siteName đã được xác thực thành công.', ['siteName' => config('setting.site.name')])

@lang('Chúng tôi đã tạo một hồ sơ mặc định cho bạn với thông tin đăng nhập sau:')

@component('mail::panel')
@lang('Mật khẩu:') **{{ $password }}**
@endcomponent

@lang('Vui lòng đăng nhập và thay đổi mật khẩu của bạn ngay lập tức để đảm bảo an toàn.')  

@lang('Nếu bạn không thực hiện hành động này, vui lòng liên hệ với bộ phận hỗ trợ của chúng tôi ngay lập tức.')

Trân trọng,  
{{ config('setting.site.name') }}
@endcomponent
