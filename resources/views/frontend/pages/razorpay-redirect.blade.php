<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pay With RazorPay</title>
</head>

<body>
    @php
        $payableAmount = getPayableAmount() * config('settings.razorpay_rate') * 100;
        $payableAmount = round($payableAmount);
    @endphp

    <form action="{{ route('razorpay.payment') }}" class="razorpay_payment_form" method="POST">
        @csrf
        <input type="hidden" class="razorpay_payment_id" name="razorpay_payment_id" value="">
    </form>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
        var options = {
            key: "{{ config('settings.razorpay_client_id') }}",
            currency: "{{ config('settings.razorpay_currency') }}",
            amount: "{{ $payableAmount }}",
            name: "{{ config('settings.site_name') }}",
            description: "payment for the product",
            image: "",
            theme: {
                color: "#F37254"
            },
            modal: {
                ondismiss: function() {
                    window.location.href = "{{ route('payment.cancel') }}";
                }
            },
            handler: function(response) {
                document.querySelector('.razorpay_payment_id').value = response.razorpay_payment_id;
                document.querySelector('.razorpay_payment_form').submit();
            }
        }

        var rzp = new Razorpay(options);

        rzp.open();
    </script>
</body>

</html>
