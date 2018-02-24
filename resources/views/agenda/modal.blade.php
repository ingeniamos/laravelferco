<div class="modal fade bd-example-modal-lg" id="agendaModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header alert-secondary">
				<h4 class="modal-title">Agendar tarea</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				
			</div>
			<div class="modal-body">
				<div id="mensajesModalAgenda"></div>
				@include('agenda.form.form1')
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				{!! link_to('#', $title='Agendar', $attributes=['id'=>'regAgenda', 'class'=>'btn btn-primary'], $secure=null) !!}
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->