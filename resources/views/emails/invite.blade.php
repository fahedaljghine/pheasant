@component('mail::message')
@if ($company)
##  {{ Auth::user()->name }} invited you to join PHEASANT ({{ $company }})
@else
##  {{ Auth::user()->name }} invited you to join PHEASANT
@endif


Hi {{ $name }},

**PHEASANT** is a simple project management and collaboration tool to help everyone in {{ $company ?? 'a company'}} to stay organized and get work done.
It's simple and easy! To join click the link below:

@component('mail::button', ['url' => url('register/' . $token)])
Join Now
@endcomponent

@endcomponent