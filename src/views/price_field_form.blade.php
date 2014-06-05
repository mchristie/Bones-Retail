
<p>
    <label><input type="radio" name="{{$field->name}}_type" value="single" onchange="switchPriceType('{{$field->name}}');" @if($field->type == 'single') checked="checked" @endif> Single</label>
    &nbsp;
    <label><input type="radio" name="{{$field->name}}_type" value="variable" onchange="switchPriceType('{{$field->name}}');" @if($field->type == 'variable') checked="checked" @endif> Variable</label>
</p>

<div class="{{$field->name}}_options" id="{{$field->name}}_options_single" @if($field->type != 'single') style="display: none;" @endif>
    <input type="text" class="form-control" id="slug" name="{{$field->name}}_single" placeholder="Single price" value="{{$field->string_data}}">
</div>

<div class="{{$field->name}}_options" id="{{$field->name}}_options_variable" @if($field->type != 'variable') style="display: none;" @endif>
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>

            @foreach($field->prices as $i => $price)
                <tr>
                    <td><input type="text" class="form-control" name="{{$field->name}}_prices[{{$i}}][title]" value="{{$price->title}}" /></td>
                    <td><input type="text" class="form-control" name="{{$field->name}}_prices[{{$i}}][price]" value="{{$price->price}}" /></td>
                </tr>
            @endforeach

            @for($i = isset($i) ? $i+1 : 0; $i < 5; $i++)
                <tr>
                    <td><input type="text" class="form-control" name="{{$field->name}}_prices[{{$i}}][title]" value="" /></td>
                    <td><input type="text" class="form-control" name="{{$field->name}}_prices[{{$i}}][price]" value="" /></td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>

@section('additional_js')
<script>
if (typeof yourFunctionName == undefined) {
    function switchPriceType(name) {
        var type = $('input[name='+name+'_type]:checked').val();

        $('.'+name+'_options').hide();
        $('#'+name+'_options_'+type).show();
    }
}

</script>
@endsection