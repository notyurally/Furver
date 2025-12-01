<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment for Adoption</title>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">Pay Adoption Fee</h1>
        <p>Pet: {{ $application->pet->name }}</p>
        <p>Fee: ${{ number_format($application->pet->adoption_fee, 2) }}</p>

        <form id="payment-form" method="POST" action="{{ route('payments.store', $application) }}">
            @csrf
            <input type="hidden" name="amount" value="{{ $application->pet->adoption_fee }}">
            
            <div class="mb-4">
                <label for="card-element" class="block text-sm font-medium text-gray-700">Card Details</label>
                <div id="card-element" class="mt-1 p-2 border border-gray-300 rounded"></div>
                <div id="card-errors" class="text-red-500 text-sm mt-1"></div>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Pay Now</button>
        </form>
    </div>

    <script>
        const stripe = Stripe('{{ env("STRIPE_KEY") }}'); // Your publishable key
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        const form = document.getElementById('payment-form');
        const cardErrors = document.getElementById('card-errors');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const { token, error } = await stripe.createToken(cardElement);
            if (error) {
                cardErrors.textContent = error.message;
            } else {
                const hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);
                form.submit();
            }
        });
    </script>
</body>
</html>