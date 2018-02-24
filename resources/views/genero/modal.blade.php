<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title">Actualizar género</h4>
			</div>
			<div class="modal-body">
				<input class="hidden" name="token" value="{{csrf_token()}}" id="token"></input>
				<input class="hidden" id="id"></input>
				@include('genero.form.genero')
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				{!! link_to('#', $title='Actualizar', $attributes=['id'=>'actualizar', 'class'=>'btn btn-primary'], $secure=null) !!}
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->