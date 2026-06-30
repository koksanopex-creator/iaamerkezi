@extends('emails.layout')

@section('content')
<div style="padding: 20px; font-family: sans-serif; color: #333; line-height: 1.6;">
    {!! $messageContent !!}
</div>
@endsection
