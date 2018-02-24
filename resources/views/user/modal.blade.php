<div class="modal fade bd-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header alert-secondary">
				<h4 class="modal-title">Editar usuario</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				
			</div>
			<div class="modal-body">
				<div id="mensajesModalUser"></div>
				{{-- <input type="hidden" name="token1" value="{{csrf_token()}}" id="token1"></input>
				<input type="hidden" id="id1"></input> --}}
				<div class="form-row align-items-center">
					@include('user.forms.form2')
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				{!! link_to('#', $title='Actualizar', $attributes=['id'=>'actUser', 'class'=>'btn btn-primary'], $secure=null) !!}
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->