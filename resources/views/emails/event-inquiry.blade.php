@extends('emails.layout')

@section('title', 'New event inquiry')
@section('preheader', 'Event inquiry from ' . $inquiry->first_name . ' ' . $inquiry->last_name . ' — ' . $inquiry->event_type)
@section('eyebrow', 'Events · Inquiry')
@section('heading', 'Someone\'s planning a night.')
@section('subheading', 'Received ' . $inquiry->created_at->format('F j, Y \a\t g:i A') . '.')

@section('content')
    <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#B8860B; font-weight:600; margin-bottom:10px;">Guest</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; width:35%;">Name</td><td style="padding:5px 0; color:#0E0E0E; font-weight:600;">{{ $inquiry->first_name }} {{ $inquiry->last_name }}</td></tr>
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Email</td><td style="padding:5px 0;"><a href="mailto:{{ $inquiry->email }}" style="color:#B8381F;">{{ $inquiry->email }}</a></td></tr>
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Phone</td><td style="padding:5px 0;">{{ $inquiry->phone }}</td></tr>
        @if($inquiry->company)
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Company</td><td style="padding:5px 0;">{{ $inquiry->company }}</td></tr>
        @endif
    </table>

    <hr style="border:0; border-top:1px solid #E9E4DA; margin:24px 0;">

    <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#B8860B; font-weight:600; margin-bottom:10px;">Event</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; width:35%;">Type</td><td style="padding:5px 0; color:#0E0E0E; font-weight:600;">{{ $inquiry->event_type }}</td></tr>
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Guests</td><td style="padding:5px 0;">{{ $inquiry->guest_count }}</td></tr>
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Date</td><td style="padding:5px 0;">{{ $inquiry->event_date->format('F j, Y') }}</td></tr>
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Time</td><td style="padding:5px 0;">{{ $inquiry->event_time }}</td></tr>
    </table>

    @if($inquiry->details)
        <hr style="border:0; border-top:1px solid #E9E4DA; margin:24px 0;">
        <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#B8860B; font-weight:600; margin-bottom:10px;">Details</div>
        <div style="background:#FAF7F1; border-left:3px solid #B8860B; padding:16px 18px; border-radius:4px; white-space:pre-line; color:#2A2A2A; line-height:1.6;">{{ $inquiry->details }}</div>
    @endif
@endsection
