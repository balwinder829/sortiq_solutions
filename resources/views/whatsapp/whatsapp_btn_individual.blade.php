@php
    $mobile = preg_replace('/\D/', '', $mobile); // remove non-digits
    if(strlen($mobile) == 10){
        $mobile = '91'.$mobile;
    }
    $name = "";
    if(!empty($name)){
        $name = "?text=Hello {{ $name }},";
    }

@endphp
<a href="https://wa.me/{{ $mobile }}{{ $name }}" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>