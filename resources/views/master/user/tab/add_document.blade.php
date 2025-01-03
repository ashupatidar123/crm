<section class="content">
    <div class="container-fluid">
        <form method="POST" id="documentFormId" enctype="multipart/form-data">
            @csrf   
            <input type="hidden" name="user_id" value="{{@$data->id}}">
            <input type="hidden" name="document_type" value="{{@$data->department_type}}">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-body row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Document Category<span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="document_id" id="document_id">
                                        <option value="">Select document category</option>
                                        @if(!empty($data_document))
                                            @foreach($data_document as $doc)
                                                <option value="{{$doc->id}}" data-name="{{$doc->category_name}}">{{$doc->category_name}} {{($doc->parent_category_id==0)?'(Parent)':'(Child)'}}</option>
                                            @endforeach    
                                        @endif        
                                    </select>
                                    <p class="text-danger" id="document_idError"></p>
                                </div>
                            </div>
                            <div class="col-md-6">    
                                <div class="form-group">
                                    <label>Document Name<span class="text-danger">*</span></label>
                                    <input type="text" name="document_name" id="document_name" class="form-control" required>
                                    <p class="text-danger" id="document_nameError"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Issue Date (Optional)</label>
                                    <div class="input-group date">
                                        <input type="text" name="issue_date" id="issue_date" class="form-control" readonly>
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                    <p class="text-danger" id="issue_dateError"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Expiry Date (Optional)</label>
                                    <div class="input-group date">
                                        <input type="text" name="expiry_date" id="expiry_date" class="form-control" readonly>
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                    <p class="text-danger" id="expiry_dateError"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Is Active<span class="text-danger">*</span></label>
                                    <select class="form-control" name="is_active" id="is_active">
                                        <option value="1">Active</option>
                                        <option value="2">In-Active</option>
                                    </select>
                                    <p class="text-danger" id="is_activeError"></p>
                                </div>
                            </div>

                            <div class="col-md-12">    
                                <div class="form-group">
                                    <label>Choose Document (pdf,csv,xls,xlsx)<span class="text-danger">*</span></label>
                                    <input type="file" name="user_document" id="user_document" class="form-control" accept=".pdf, .csv, .xls, .xlsx">
                                    <p class="text-danger" id="user_documentError"></p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button id="documentSubmitButton" type="button" class="btn btn-primary">Submit</button>
                                    <button type="button" class="btn btn-danger referesh_form" onclick="return referesh_form();">Refresh</button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="show_message"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<script>
    $(document).ready(function(){
        $('.select2').select2();
    });
</script>
@include('script.comman_js')
@include('script.country_state_city_js')
@include('script.user_js')