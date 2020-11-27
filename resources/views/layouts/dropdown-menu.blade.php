<li class="nav-item dropdown">
    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        {{ $title }}
    </a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="{{ $add }}">
            <i class="glyphicon glyphicon-users"></i>
            {{ __('navbar.addNew') }}
        </a>
        <a class="dropdown-item" href="{{ $list }}">
            {{ __('navbar.list') }}
        </a>
    </div>
</li>