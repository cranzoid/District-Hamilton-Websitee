@extends('emails.layout')

@section('title', 'New contact submission')
@section('preheader', 'New message from the contact form — ' . $name)
@section('eyebrow', 'Inbox')
@section('heading', 'A note from the site.')
@section('subheading', 'Someone just wrote in via the website contact form.')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; width:35%;">Name</td><td style="padding:6px 0; color:#0E0E0E; font-weight:600;">{{ $name }}</td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Email</td><td style="padding:6px 0;"><a href="mailto:{{ $email }}" style="color:#B8381F;">{{ $email }}</a></td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Phone</td><td style="padding:6px 0;">{{ $phone ?? '—' }}</td></tr>
        @if(!empty($subject))
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Subject</td><td style="padding:6px 0;">{{ $subject }}</td></tr>
        @endif
    </table>

    <hr style="border:0; border-top:1px solid #E9E4DA; margin:24px 0;">

    <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; color:#B8860B; margin-bottom:8px;">Message</div>
    <div style="background:#FAF7F1; border-left:3px solid #B8860B; padding:16px 18px; border-radius:4px; white-space:pre-line; color:#2A2A2A; line-height:1.6;">{{ $message }}</div>

    <p style="margin-top:28px; font-size:13px; color:#777;">Reply directly to this email to respond to the customer.</p>
@endsection
