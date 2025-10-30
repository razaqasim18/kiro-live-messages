@extends('layouts.master')
@section('title', __('Packages'))

@section('css')
    <link href="{{ URL::asset('/assets/libs/admin-resources/admin-resources.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            Packages
        @endslot
    @endcomponent

    <div class="card mb-xl-0">
        <div class="card-body">
            <div class="container mt-5">
                <h3 class="text-center">Buy Package: {{ $package->name }}</h3>
                <p class="text-center">Price: ${{ $package->price }} | Coins: {{ $package->coins }}</p>

                @if (Session::has('success'))
                    <div class="alert alert-success text-center">{{ Session::get('success') }}</div>
                @elseif (Session::has('error'))
                    <div class="alert alert-danger text-center">{{ Session::get('error') }}</div>
                @endif

                <form id="payment-form" action="{{ route('package.payment.process', $package->id) }}" method="POST"
                    data-stripe-publishable-key="{{ config('services.stripe.key') ?? env('STRIPE_KEY') }}"
                    class="require-validation">
                    @csrf

                    <div class="form-group mb-3">
                        <label>Card Number</label>
                        <input type="text" class="form-control card-number" placeholder="4242 4242 4242 4242"
                            autocomplete="off" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>CVC</label>
                        <input type="text" class="form-control card-cvc" placeholder="123" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label>Exp. Month</label>
                            <input type="text" class="form-control card-expiry-month" placeholder="MM" required>
                        </div>
                        <div class="col">
                            <label>Exp. Year</label>
                            <input type="text" class="form-control card-expiry-year" placeholder="YYYY" required>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100" type="submit">
                        Pay ${{ $package->price }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- Stripe v2 library --}}
    <script src="https://js.stripe.com/v2/"></script>
    <script>
        $(function() {
            const $form = $(".require-validation");

            $form.on('submit', function(e) {
                e.preventDefault();

                Stripe.setPublishableKey($form.data('stripe-publishable-key'));

                Stripe.card.createToken({
                    number: $('.card-number').val(),
                    cvc: $('.card-cvc').val(),
                    exp_month: $('.card-expiry-month').val(),
                    exp_year: $('.card-expiry-year').val()
                }, stripeResponseHandler);

                return false;
            });

            function stripeResponseHandler(status, response) {
                if (response.error) {
                    alert(response.error.message);
                } else {
                    const token = response.id;
                    $form.append(`<input type='hidden' name='stripeToken' value='${token}'/>`);
                    $form.get(0).submit();
                }
            }
        });
    </script>
@endsection
