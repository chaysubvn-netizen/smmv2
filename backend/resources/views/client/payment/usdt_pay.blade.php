<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment...</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f3f4f6;
            margin: 0;
            color: #374151;
            flex-direction: column;
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body onload="document.getElementById('payForm').submit()">
    <div class="loader"></div>
    <p>Please wait, we are redirecting you to the payment gateway...</p>
    
    <form id="payForm" action="https://fpayaz.com/checkout" method="post">
        <input type="hidden" name="KEY" value="{{ $key }}">
        <input type="hidden" name="AMOUNT" value="{{ $amount }}">
        <input type="hidden" name="BAGGAGE_FIELDS" value="ACCOUNT_ID ACCOUNT_USER">
        <input type="hidden" name="ACCOUNT_ID" value="{{ $recharge->request_id }}">
        <input type="hidden" name="ACCOUNT_USER" value="{{ Auth::user()->username }}">
        <input type="hidden" name="NOTE" value="{{ $recharge->description }}">
        <input type="hidden" name="REDIRECT_URL" value="{{ route('addfuns', ['success' => 1]) }}">
        <input type="submit" name="PAYMENT_METHOD" value="Pay Now" style="display:none;">
    </form>
</body>
</html>
