@if($patient['due']>0)
    <span class="text-danger text-bold">
        {{$patient['due']}}
    </span>
@else 
    <span class="text-success">
        {{$patient['due']}}
    </span>
@endif