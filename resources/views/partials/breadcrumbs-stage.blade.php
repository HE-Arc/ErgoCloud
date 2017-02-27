
<div class="breadcrumps-stage">
    <div class="container">
        <ol class="breadcrumb">

            @foreach ($breadcrumps as $name => $link)
               <li><a href="{{ $link }}">{{ $name }}</a></li>
            @endforeach

        </ol>
    </div>
</div>

