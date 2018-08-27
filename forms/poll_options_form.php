<!-- Modal -->
<div class="modal fade" id="pollOptionsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit Poll Options</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">

            <div id="poll-options-stage">
                options go here.
                
            
            </div>

            <form class="form-horizontal" id="new_option_form">
                <input type="hidden" id="new_option_poll_id" name="new_option_poll_id" value="">
                <input type="hidden" name="object" value="poll_option">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label for="new_option" class="control-label col-xs-4">Add New Option</label> 
                    <div class="col-xs-8">
                    <div class="input-group">
                        <input id="new_option" name="new_option" type="text" class="form-control"> 
                    </div>
                    </div>
                </div> 
                <div class="form-group row">
                    <div class="col-xs-offset-4 col-xs-8">
                    <button class="btn btn-primary new_option_form_submit" type="submit">Submit</button>
                    </div>
                </div>
            </form>

        </div>
    
        </div>
    </div>
</div>