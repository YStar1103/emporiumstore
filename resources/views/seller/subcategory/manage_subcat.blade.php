@extends('admin.layout.layout') @section('content')
@php
use App\Category;
@endphp


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manage Subcategory</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/seller/add_subcat')}}" class="btn btn-primary"><i class="fa fa-plus">&nbsp;</i>Add New</a></li>

                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- SELECT2 EXAMPLE -->
            <div class="card card-default">

                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Subcategory</th>
                                <th>Category</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php 
                            $srn=1;
                        @endphp
                           @if($subcategory)
                                @foreach($subcategory as $allsubcat)
                                <tr>
                                <td>{{$srn}}</td>
                                <td>{{$allsubcat->name}}</td>
                                
                        @php 
                        $category_data = Category::getproduct_category($allsubcat->cat_id);
                        $category = $category_data->catname;
                        @endphp
                                <td>{{$category}}</td>
                                <td>{{date('d-m-Y',strtotime($allsubcat->created_date))}}</td>
                                <td><a href="{{url('/seller/edit_subcat')}}/{{$allsubcat->id}}">Edit</a> &nbsp; <a href="#" class="delete_btn" id="{{$allsubcat->id}}">Delete</a></td>
                            </tr>
                            @php $srn++ @endphp
                                @endforeach
                           @endif
                            
                           
                            
                            

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>S.No.</th>
                                <th>Subcategory</th>
                                <th>Category</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>


                    </table>

                </div>

            </div>

        </div>
    </section>
</div>
@section('script')

<!-- page script -->
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    $(document).ready(function() {
        $('#example1').DataTable();
    });
</script>
<script>
    $(document).ready(function() {
        $('.delete_btn').click(function() {
            var id = $(this).attr('id');
            if (confirm('Are you sure want to delete ?')) {
                document.location.href = '{{url("/seller/deletesubcat")}}' + '/' + id;
            } else {
                return false;
            }
        });
    });
</script>
@stop @endsection