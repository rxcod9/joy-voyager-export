<div class="btn-group" role="group">
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="{{ $action->getIcon() }}"></i> <span class="hidden-xs hidden-sm">{{ $action->getTitle() }}</span>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a class='export-by-writer' href="#" data-writer-type="Xls">Xls</a></li>
            <li><a class='export-by-writer' href="#" data-writer-type="Xlsx">Xlsx</a></li>
            <li><a class='export-by-writer' href="#" data-writer-type="Ods">Ods</a></li>
            <li><a class='export-by-writer' href="#" data-writer-type="Csv">Csv</a></li>
            <li><a class='export-by-writer' href="#" data-writer-type="Html">Html</a></li>
            @if(class_exists('TCPDF'))
                <li><a class='export-by-writer' href="#" data-writer-type="Tcpdf">Tcpdf</a></li>
            @endif
            @if(class_exists('Dompdf\Dompdf'))
            <li><a class='export-by-writer' href="#" data-writer-type="Dompdf">Dompdf</a></li>
            @endif
            @if(class_exists('Mpdf\Mpdf'))
            <li><a class='export-by-writer' href="#" data-writer-type="Mpdf">Mpdf</a></li>
            @endif
        </ul>
    </div>
</div>
<form id="bulk_export_form" method="post" action="{{ route('voyager.'.$dataType->slug.'.action') }}" style="display:none;">
    {{ csrf_field() }}
    <button type="submit"><i class="{{ $action->getIcon() }}"></i> {{ $action->getTitle() }}</button>
    <input type="hidden" name="action" value="{{ get_class($action) }}">
    <input type="hidden" name="ids" value="" class="selected_ids">
</form>

@push('javascript')
<script>
    $(function() {
        var exportAction = `{{ route('voyager.'.$dataType->slug.'.action') }}`;
        $('.export-by-writer').click(function() {
            var writerType = $(this).data('writer-type');
            $('#bulk_export_form').attr('action', exportAction + '?writerType=' + writerType);
            $('#bulk_export_form').submit();
        })
    });
</script>
@endpush