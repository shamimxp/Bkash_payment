@extends($template.'layouts.master')
@section('content')
    <div style="text-align: center;">
        <br><br>
        <form action="{{ route('url-post-refund') }}" method="POST">
            @csrf
            <label for="paymentID">PaymentID:</label>
            <input type="text" id="paymentID" name="paymentID" required ><br><br>
            <label for="trxID">TrxID:</label>
            <input type="text" id="trxID" name="trxID" required ><br><br>
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" required ><br><br>
            <input type="submit" value="Submit">
        </form>
    </div>
    <br><br>
    <div style="text-align: center;">
        @if(isset($response))
            {{ $response }}
        @endif
    </div>
@endsection
