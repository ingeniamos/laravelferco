<div class="form-group">
	{!!Form::label('Nombre')!!}
	{!!Form::text('nombre',null,['id'=>'nombre','class'=>'form-control'])!!}
</div>
<div class="form-group">
	{!!Form::label('Apellido')!!}
	{!!Form::text('apellido',null,['id'=>'apellido','class'=>'form-control'])!!}
</div>
<div class="form-group">
	{!!Form::label('Cédula')!!}
	{!!Form::text('cedula',null,['id'=>'cedula','class'=>'form-control'])!!}
</div>
<div class="form-group">
	{!!Form::label('Teléfono')!!}
	{!!Form::text('telefono',null,['id'=>'telefono','class'=>'form-control'])!!}
</div>
<div class="form-group">
	{!!Form::label('E-mail')!!}
	{!!Form::text('email',null,['id'=>'email','class'=>'form-control'])!!}
</div>
{{-- <div class="form-group">
	{!!Form::label('Puesto de votación')!!}
	{!!Form::text('puesto_id',null,['id'=>'puesto_id','class'=>'form-control'])!!}
</div> --}}
<div class="form-group">
	{!!Form::label('Puesto de votación')!!}
	{!!Form::select('puesto',$puestos,null,['id'=>'puesto_id','class'=>'form-control'])!!}
</div>
<div class="form-group">
	{!!Form::label('Coordinador')!!}
	{{-- {!!Form::text('coordinador_id',null,['id'=>'coordinador_id','class'=>'form-control'])!!} --}}
	{!!Form::select('coordinador',$users,null,['id'=>'coordinador_id','class'=>'form-control'])!!}
</div>