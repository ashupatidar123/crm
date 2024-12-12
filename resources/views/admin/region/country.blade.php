@extends('layouts.head')

@section('title') All Country @endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!-- <h1>DataTables</h1> -->
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">Country</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- /.card -->
                    <div class="card card-primary">
                        <div class="card-header card_header_color">
                            <h3 class="card-title">All Country List</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body responsive">
                            <table id="tableList" class="table responsive table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>ISO 3</th>
                                        <th>Numeric Code</th>
                                        <th>Capital</th>
                                        <th>Currency</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>ISO 3</th>
                                        <th>Numeric Code</th>
                                        <th>Capital</th>
                                        <th>Currency</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <!-- Modal -->
    <section class="content">
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Country</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Some text in the modal.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script>
    var csrf_token = "{{ csrf_token() }}";
    $(document).ready(function() {
        data_table_list();
        $("#myBtn").click(function(){
            $("#myModal").modal();
          });
    });

    function data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("region/country_list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'iso3' },
                { data: 'numeric_code' },
                { data: 'capital' },
                { data: 'currency' },
                { data: 'created_at', type: 'date' },
                { data: 'action' }
            ],
            "order": [[1, 'ASC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [7],"orderable": false}]
        });
    }

    function reset_filter(){
        $('#start_limit').val('');
        $('#end_limit').val('');
        data_table_list();
    }

    function country_delete(p_id){
        Swal.fire({
            title: "Are you sure to delete?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if(result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{url('region/country_delete')}}",
                    data: {p_id},
                    headers: {
                        'X-CSRF-TOKEN': csrf_token
                    },
                    dataType:"JSON",
                    success: function (resp) {
                        if(resp.status == 'success'){
                            data_table_list();
                            swal_success(resp.message,1800);
                        }else{
                            swal_error(resp.message,1800); 
                        }
                    }
                });
            }
        });        
    }

    function country_edit(){
        $("#myModal").modal();
    }
</script>
@include('script.comman_js')
@endsection