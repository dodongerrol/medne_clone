@include('common.home_header')

<div class="modal fade" id="preview-claim" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Transaction Details</h4>
      </div>
      <div class="modal-body">
        <p>Name: <span></span></p>
        <p>Procedure: <span></span></p>
        <p>Date of Transaction: <span></span></p>
        <p>Credits Used: <span></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
	$(document).ready(function( ) {
		$('#preview-claim').modal('show');
	});
</script>

@include('common.footer')