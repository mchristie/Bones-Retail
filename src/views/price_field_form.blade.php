
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
                <th>SKU</th>
                <th>Title</th>
                <th>Price</th>
                <th>Available</th>
            </tr>
        </thead>
        <tbody>

            @foreach($field->variations as $i => $variation)
                <tr>
                    <td><input type="text" class="form-control" name="{{$field->name}}_variations[{{$i}}][sku]" value="{{$variation->sku}}" /></td>
                    <td><input type="text" class="form-control" name="{{$field->name}}_variations[{{$i}}][title]" value="{{$variation->title}}" /></td>
                    <td><input type="text" class="form-control" name="{{$field->name}}_variations[{{$i}}][price]" value="{{$variation->price}}" /></td>
                    <td><input type="text" class="form-control" name="{{$field->name}}_variations[{{$i}}][available]" value="{{$variation->available}}" placeholder="Default" /></td>
                </tr>
            @endforeach

            @for($i = isset($i) ? $i+1 : 0; $i < 5; $i++)
                <tr>
                    <td><input type="text" class="form-control" name="{{$field->name}}_variations[{{$i}}][sku]" value="" /></td>
                    <td><input type="text" class="form-control" name="{{$field->name}}_variations[{{$i}}][title]" value="" /></td>
                    <td><input type="text" class="form-control" name="{{$field->name}}_variations[{{$i}}][price]" value="" /></td>
                    <td><input type="text" class="form-control" name="{{$field->name}}_variations[{{$i}}][available]" value="" placeholder="Default" /></td>
                </tr>
            @endfor
        </tbody>
    </table>
    <p class="help-block">If the available field is left blank for any variations, the product-wide quantity field will be used.</p>
</div>

<div id="{{$field->name}}_available">
    <label for="{{$field->name}}_available">Quantity available</label>
    <input type="text" class="form-control" id="{{$field->name}}_available" name="{{$field->name}}_available" placeholder="Leave blank for unlimited availablity" value="{{$field->available}}">
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