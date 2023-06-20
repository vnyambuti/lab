@if($doctor['due']>0)
    <span class="text-danger">{{new App\Traits\formated_price( $doctor['due'])}}</span>
@else 
    <span class="text-success">{{$doctor['due']}}</span>
@endif