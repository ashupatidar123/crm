@extends('layouts.header')

@section('title') Home @endsection

@section('content')
<div class="container">
    <h2>Login {{@Auth::user()->name}}</h2>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">Logout</button>
    </form>
    <h2>Use List</h2>
    <!-- <input type="text" id="start_limit" placeholder="Start limit">
    <input type="text" id="end_limit" placeholder="End limit" onkeyup="return data_table_list();">
    <button type="button" class="btn btn-danger btn-sm" id="reset_filter" onclick="return reset_filter();">Reset</button> -->

    <div class="table-responsive">
        <table class="table" id="tableList" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    // if ($.fn.dataTable.isDataTable('#myTable')) {
    //     $('#tableList').DataTable().clear().destroy();
    // }

    $(document).ready(function() {
        data_table_list();
    });

    function data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            ajax: {
                url: '{{url('userList')}}',
                type: 'GET',
                data:{start_limit,end_limit},
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'email' },
                { data: 'mobile' },
                { data: 'amount' },
                { data: 'created_at', type: 'date' }
            ],
            order: [[0, 'DESC']],
            "lengthMenu": [10, 25, 50,100,150,200,500],
            "pageLength": pageLength,
            //"dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print']
        });
    }

    function reset_filter(){
        $('#start_limit').val('');
        $('#end_limit').val('');
        data_table_list();
    }
</script>
@endsection