<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { margin: 0; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; margin: 0; padding: 0; }
        .container { padding: 2.54cm; position: relative; }
        .kop-spacing { height: 3cm; width: 100%; }
        .page-break { page-break-after: always; }
        table { width: 100%; border-collapse: collapse; }
        table, td, th { border: 1px solid black; padding: 4px; }
    </style>
</head>
<body>
    @php $pages = explode('<div class="page-break-divider"></div>', $isiSurat); @endphp
    @foreach($pages as $index => $content)
        <div class="container {{ !$loop->last ? 'page-break' : '' }}">
            @if($index == 0 && $template->use_kop == 1)
                <div class="kop-spacing"></div>
            @endif
            <div class="isi-surat">
                {!! $content !!}
            </div>
        </div>
    @endforeach
</body>
</html>
