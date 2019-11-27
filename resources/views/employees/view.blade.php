@extends('layouts.master')

@section('content')
<div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <!--  <a class="navbar-brand" href="#">Navbar</a>-->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor03" aria-controls="navbarColor03" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarColor03">
            <ul class="navbar-nav mr-auto">
                @if($loginGuard)
                <li class="nav-item @if($menu_selected == 'companies') active @endif">
                    <a class="nav-link" href="{{ route('admin.companies') }}">Companies 
                        <span class="sr-only">(current)</span>
                    </a>
                </li>
                @endif
                <li class="nav-item @if($menu_selected == 'employees') active @endif" >
                    <a class="nav-link" href="{{ route($loginGuard.'employees') }}">Employees </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">List of Employees
                    <button type="button" class="btn btn-sm btn-primary employee_form" data-id="-1" data-toggle="modal" data-target="#modal-lg" style="float: right">Add Employee</button>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger alert-block delete_row_error" style="display: none;">
                        <button type="button" class="close" data-dismiss="alert">×</button>	
                        <strong>Row deleted successfully.</strong>
                        <script>
                            $(document).ready(function(){
                                setTimeout(function(){ 
                                    $('.delete_row_error').hide();
                                }, 10000);    
                            });
                        </script>
                    </div>

                    <div class="body">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:1%">#</th>
                                        <th>Fullname</th>
                                        <th>Company</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content form-content">

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script>
    $(function () {
        var Data_table = $("#example").DataTable({
            responsive: true,
            serverSide: true,
            "columnDefs": [
                {"targets": 0, orderable: false},
                {"targets": 5, orderable: false},
            ],
            "order": [],
            ajax: {
                url: "{{route($loginGuard.'employees.getdata')}}", // json datasource
                type: "POST", // method  , by default get
                data: {_token: $('meta[name="csrf-token"]').attr('content')},
//                complete: function () {
////                    $('.top_loader').hide();
//                },
//                error: function () {  // error handling
//
//                },
//                beforeSend: function () {
////                    $('.top_loader').show();
//                }
            }
        });

        $(document).on('click', '.employee_form', function () {
            var form_id = $(this).data('id');
            $.ajax({
                url: "{{route($loginGuard.'employees.formdata')}}", // json datasource
                type: "POST", // method  , by default get
                data: {_token: $('meta[name="csrf-token"]').attr('content'), id: form_id},
                success: function (data) {
                    $('.form-content').html('');
                    $('.form-content').append(data.html);
                }
            });
        });

        $(document).on('click', '.delete_row', function () {
            var get_href = $(this).data('href');
            if(confirm('Are you sure, You want to delete?' )){
                $.ajax({
                    url: get_href, // json datasource
                    type: "POST", // method  , by default get
                    data: {_token: $('meta[name="csrf-token"]').attr('content')},
                    success: function (data) {
                        if (data) {
                            Data_table.ajax.reload();
                            $('.delete_row_error').attr('style','display:""');
                        }
                    }
                });
            }

        });

    });
</script>
@endsection
