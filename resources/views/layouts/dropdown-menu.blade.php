@if(! isset($show) || $show)
    <li class="nav-item dropdown">
        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
            {{ $title }}
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            @foreach($links as $link)
                @if(! isset($link['show']) || $link['show'])
                    <a class="dropdown-item" href="{{ $link['route'] }}">
                        {{ $link['label'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </li>
@endif
