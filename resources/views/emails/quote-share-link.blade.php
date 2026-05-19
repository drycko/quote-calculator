<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $subjectText }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222; line-height: 1.5;">
    <p>Hello {{ $quote->client_contact_name ?: $quote->client_name ?: 'there' }},</p>

    @foreach(preg_split("/\r\n|\n|\r/", $messageText) as $paragraph)
        @if(trim($paragraph) !== '')
            <p>{{ $paragraph }}</p>
        @endif
    @endforeach

    <p>
        <a href="{{ $shareUrl }}" style="display: inline-block; padding: 10px 16px; background: #0d6efd; color: #fff; text-decoration: none; border-radius: 4px;">
            View and approve quote
        </a>
    </p>

    <p>
        Quote: <strong>{{ $quote->quote_number }}</strong><br>
        Total: <strong>{{ format_money($quote->total_inc_vat) }}</strong>
    </p>

    @if($quote->salesperson_name || $quote->salesperson_email)
        <p>
            Regards,<br>
            {{ $quote->salesperson_name ?: config('app.name') }}<br>
            @if($quote->salesperson_email)
                <a href="mailto:{{ $quote->salesperson_email }}">{{ $quote->salesperson_email }}</a>
            @endif
        </p>
    @endif

    <p style="font-size: 12px; color: #666;">
        If the button does not work, open this link: <br>
        <a href="{{ $shareUrl }}">{{ $shareUrl }}</a>
    </p>
</body>
</html>
