
<!-- Modal -->
<div class="modal fade" id="editPollModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit Poll</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">

            <form id="edit_poll_form" class="form-horizontal">
                <input type="hidden" name="object" id="poll" value="poll">
                <input type="hidden" name="action" id="action" value="update">
                <input type="hidden" name="poll_id" id="edit_form_poll_id" value="">
                <div class="form-group">
                    <label for="poll_question" class="control-label col-xs-4">Poll Question</label> 
                    <div class="col-xs-8">
                    <textarea id="edit_form_poll_question" name="poll_question" cols="40" rows="5" required="required" class="form-control"></textarea>
                    </div>
                </div> 
                <div class="form-group row">
                    <div class="col-xs-offset-4 col-xs-8">
                    <button name="submit" type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>

        </div>
    
        </div>
    </div>
</div>