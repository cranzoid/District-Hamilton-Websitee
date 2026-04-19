@extends('emails.layout')

@section('title', 'New contact submission')
@section('preheader', 'New contact form submission from ' . $submission->first_name . ' ' . $submission->last_name)
@section('eyebrow', 'Inbox · Contact form')
@section('heading', 'A new message for you.')
@section('subheading', 'Received ' . $submission->created_at->format('F j, Y \a\t g:i A') . '.')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; width:35%;">Name</td><td style="padding:6px 0; color:#0E0E0E; font-weight:600;">{{ $submission->first_name }} {{ $submission->last_name }}</td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Email</td><td style="padding:6px 0;"><a href="mailto:{{ $submission->email }}" style="color:#B8381F;">{{ $submission->email }}</a></td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Phone</td><td style="padding:6px 0;">{{ $submission->phone ?: '—' }}</td></tr>
        @if($submission->subject)
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Subject</td><td style="padding:6px 0;">{{ $submission->subject }}</td></tr>
        @endif
    </table>

    <hr style="border:0; border-top:1px solid #E9E4DA; margin:24px 0;">

    <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; color:#B8860B; margin-bottom:8px;">Message</div>
    <div style="background:#FAF7F1; border-left:3px solid #B8860B; padding:16px 18px; border-radius:4px; white-space:pre-line; color:#2A2A2A; line-height:1.6;">{{ $submission->message }}</div>
@endsection
