@extends('layout')

@section('content')
<div class="container-fluid">

 	<div class="row align-items-center mb-5">
        <div class="col-md-6 text-center text-md-left mb-4 mb-md-0">
            <img src="/pete.png" alt="WordPress Pete" class="img-responsive" style="max-height:200px">
        </div>
        <div class="col-md-6 d-flex flex-column justify-content-center">
            <h2 class="mb-1">WordPress backups</h2>
            <p class="text-muted mb-0">Create reliable backups by exporting your sites in WordPress Pete format. Built-in systems can fail — this gives you full control and peace of mind, ensuring your clients' data is always protected. </p>
        </div>
    </div>
    
    {{-- flash messages --------------------------------------------------- --}}
    <div id="update_area_info"></div>

    {{-- backups table ---------------------------------------------------- --}}
    <div class="row" style="margin-top: 20px">
        <div class="col-12">
            <div class="panel panel-default">
                <div class="panel-heading d-flex justify-content-between align-items-center">
                    <h3 class="panel-title mb-0">Saved Backups</h3>
					<p class="text-muted">Create, download, and restore project snapshots in seconds.</p>
                    @if($backups->count())
                        <small class="text-muted">{{ $backups->count() }} total</small>
                    @endif
                </div>

                <div class="table-responsive">
                    @if($backups->count())
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
									<th>Label</th>
                                    <th>URL</th>
                                    <th>File</th>
                                    <th class="text-right" width="220">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($backups as $backup)
                                    <tr>
										<td>{{ $backup->schedulling }}</td>
                                        <td><a href="http://{{ $backup->url }}" target="_blank">{{ $backup->url }}</a></td>
                                        <td>/var/www/html/Pete/backups/{{ $backup->site_id }}/{{ $backup->file_name }}</td>
                                        <td class="text-right" style="vertical-align:middle;">
                                            <div class="btn-group btn-group-sm" role="group" style="display:flex;align-items:center;gap:.25rem;">
                                                {{-- Restore -------------------------------------------------- --}}
                                                <button type="button"
                                                        class="btn btn-default restore_backup_action"
                                                        data-backup-id="{{ $backup->id }}">
                                                    <span class="glyphicon glyphicon-import"></span> Restore
                                                </button>

                                                {{-- Delete --------------------------------------------------- --}}
                                                <form action="/wordpress_backups/destroy"
                                                      method="POST"
                                                      class="m-0 p-0 d-inline-flex align-items-center"
                                                      onsubmit="return confirm('Delete this backup? This action cannot be undone.');">
                                                    @csrf
                                                    <input type="hidden" name="backup_id" value="{{ $backup->id }}">
                                                    <button type="submit" class="btn btn-danger">
                                                        <span class="glyphicon glyphicon-trash" style="font-size:10px;"></span> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="panel-body text-center">
                            <p class="lead mb-0">No backups found — create one from the “My WordPress Sites” page.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- loader modal (global) ---------------------------------------------- --}}
@include('sites.partials.modals')

@push('scripts')
<script>
(function ($) {
    'use strict';

    function loader(on) { $('#loadMe').modal(on ? 'show' : 'hide'); }

    /** Restore backup ------------------------------------------------- */
    $(document).on('click', '.restore_backup_action', function () {
        const backupId = $(this).data('backup-id');
        BootstrapDialog.show({
            title: 'Restore Backup',
            message: '<label>Domain</label><br><input class="form-control" id="backup_domain" placeholder="example.com">',
            buttons: [{
                label: 'Restore',
                cssClass: 'btn-primary',
                action(dialog) {
                    const domain = $('#backup_domain').val().trim();
                    if (!domain) return;
                    loader(true);
                    $.get('/wordpress_backups/restore', {backup_domain: domain, backup_id: backupId}, function (res) {
                        loader(false);
                        if (res.message) {
                            $.notify({message: res.message}, {type: 'info', delay: 4000});
                        } else {
                            window.location.href = '/sites?success=true';
                        }
                    });
                    dialog.close();
                }
            }]
        });
    });

})(jQuery);
</script>
@endpush
@endsection
