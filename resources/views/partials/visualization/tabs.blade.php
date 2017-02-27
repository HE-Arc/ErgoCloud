<ul class="nav nav-tabs">

    @foreach(array(
            'Statistiques' => URL::to('/test/'.$test->id.'/trial/'.$trial->name.'/statistics'),
            'Heatmap' => URL::to('/test/'.$test->id.'/trial/'.$trial->name.'/heatmap'),
            'Scanpath' => URL::to('/test/'.$test->id.'/trial/'.$trial->name.'/scanpath')
            ) as $page_name => $url)
        
        <li role="presentation" 
        
        @if($page_name == $actual_page)
            class="active"
        @endif
        
        ><a href="{{ $url }}">{{ $page_name }}</a></li>
        
    @endforeach
</ul>