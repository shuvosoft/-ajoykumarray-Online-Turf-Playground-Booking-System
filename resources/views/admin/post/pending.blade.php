@extends('layouts.backend.app')

@section('title','Post')

@push('css')
    <!-- JQuery DataTable Css -->
    <link href="{{ asset('assets/backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">

        <!-- Exportable Table -->
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>
                            ALL Booking Request
                            <span class="badge bg-blue">{{ $posts->count() }}</span>
                        </h2>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Playground Title</th>
                                    <th>Playground Size</th>
                                    <th>Playground<br> People Capacity </th>
                                    <th>Booking time</th>
                                    <th>Status</th>
                                    <th>Booking Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>

                                    <th>Playground Title</th>
                                    <th>Playground Size</th>
                                    <th>Playground<br> People Capacity </th>
                                    <th>Booking time</th>
                                    <th>Status</th>
                                    <th>Booking Status</th>
                                    <th>Action</th>
                                </tr>
                                </tfoot>
                                <tbody>
                                    @foreach($posts as $key=>$post)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ str_limit($post->title,'10') }}</td>
                                            <td>{{ $post->user->name }}</td>
                                            <td>{{ str_limit($post->title,'10') }}</td>
                                            <td>{{ $post->size }}</td>
                                            <td>{{ $post->people_capacity }}</td>
                                            <td>{{ $post->start_time }} to {{$post->end_time}}</td>

                                            <td>
                                                @if($post->status == true)
                                                    <span class="badge bg-blue">Published</span>
                                                @else
                                                    <span class="badge bg-pink">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($post->b_status == 0)
                                                    <span class="badge bg-yellow">Processing</span>
                                                @elseif($post->b_status == 1)
                                                    <span class="badge bg-pink">Booking Accepted</span>
                                                @elseif($post->b_status == 2)
                                                    <span class="badge bg-green">Booking Completed</span>
                                                @else
                                                    <span class="badge bg-red">Booking Cancel</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($post->status == false)
                                                    <button type="button" class="btn btn-success waves-effect" onclick="approvePost({{ $post->booking_id }})" title="you want to Accept it !">
                                                        <i class="material-icons">done</i>
                                                    </button>
                                                    <form method="post" action="{{ route('admin.booking.approve',$post->booking_id) }}" id="approval-form-{{ $post->booking_id }}" style="display: none">
                                                        @csrf
                                                        @method('PUT')
                                                    </form>

                                                    @elseif($post->status == 1)
                                                        <button type="button" class="btn btn-info waves-effect" onclick="approvePost({{ $post->booking_id }})" title="Are you Booking Completed !">
                                                            <i class="material-icons">done</i>
                                                        </button>
                                                        <form method="post" action="{{ route('admin.booking.completed',$post->booking_id) }}" id="approval-form-{{ $post->booking_id }}" style="display: none">
                                                            @csrf
                                                            @method('PUT')
                                                        </form>
                                                 @endif
                                                <a href="{{ route('admin.bookingView',$post->booking_id) }}" class="btn btn-info waves-effect" >
                                                    <i class="material-icons">visibility</i>
                                                </a>

{{--                                                @if($post->status != 2)--}}
                                                    <button type="button" class="btn btn-danger waves-effect" onclick="approvePost({{ $post->booking_id }})" title="you want to cancel it">
                                                        <i class="material-icons">done</i>
                                                    </button>
                                                    <form method="post" action="{{ route('admin.booking.cancel',$post->booking_id) }}" id="approval-form-{{ $post->booking_id }}" style="display: none">
                                                        @csrf
                                                        @method('PUT')
                                                    </form>
{{--                                                 @endif--}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- #END# Exportable Table -->
    </div>
@endsection

@push('js')
    <!-- Jquery DataTable Plugin Js -->
    <script src="{{ asset('assets/backend/plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/buttons.print.min.js') }}"></script>

    <script src="{{ asset('assets/backend/js/pages/tables/jquery-datatable.js') }}"></script>
    <script src="https://unpkg.com/sweetalert2@7.19.1/dist/sweetalert2.all.js"></script>
    <script type="text/javascript">
        function deletePost(id) {
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('delete-form-'+id).submit();
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swal(
                        'Cancelled',
                        'Your data is safe :)',
                        'error'
                    )
                }
            })
        }
        function approvePost(id) {
            swal({
                title: 'Are you sure?',
                text: "You went to approve this post ",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, agree it!',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('approval-form-'+ id).submit();
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swal(
                        'Cancelled',
                        'The post remain pending :)',
                        'info'
                    )
                }
            })
        }
    </script>
@endpush