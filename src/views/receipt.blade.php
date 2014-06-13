<!DOCTYPE html>
<html lang="en">
<head>

    <meta name="viewport" content="initial-scale=1, maximum-scale=1, width=device-width, user-scalable=no">
    <meta charset="utf-8">
    <title>{{$site->title}} - Receipt</title>

    <style type="text/css">
        th, td {
            padding: 10px;
        }
    </style>

</head>
<body>

    <div class="row">
        <div class="col-md-6">
            <h1>{{$site->title}} - Receipt</h1>
        </div>
        <div class="col-md-6">
            <!-- Address -->
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>
                                {{$item->title}}
                                @if($item->price->variation)
                                    <br>
                                    <span class="text-muted">{{$item->price->variation_title}}</span>
                                @endif
                            </td>
                            <td>
                                {{$item->price->quantity}}
                            </td>
                            <td>
                                @if($item->price->variation)
                                    &pound; {{$item->price->variation_price}}
                                @else
                                    &pound; {{$item->price}}
                                @endif
                            </td>
                            <td>
                                &pound; {{$item->price->total()}}
                            </td>
                        <tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>{{Basket::totalItems()}}</td>
                        <td></td>
                        <td>&pound; {{Basket::total()}}</td>
                </tbody>
            </table>

            <h4>Status: Paid</h4>

        </div>
    </div>

</body>
</html>