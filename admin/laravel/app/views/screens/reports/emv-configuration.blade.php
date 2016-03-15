<HTML>
    <head>
        <title>EMV Terminal Configuration Report - {{$terminal}}</title>
    </head>
    <body>
    @if($missingSettings)
        Unable to connect to the terminal due to missing settings. Please contact Club Speed support for assistance.
    @endif

    @if($missingReport)
        Unable to retrieve the report. Please contact Club Speed support for assistance.
    @else
        <pre>{{$report}}</pre>
    @endif
    </body>
</HTML>