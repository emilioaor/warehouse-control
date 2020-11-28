<form action="{{ $route }}">
    <div class="row">

        <div class="col-sm-8 col-md-6 form-group input-group">
            <input
                    type="text"
                    name="search"
                    class="form-control"
                    value="{{ Request::has('search') ? Request::get('search') : '' }}"
                    maxlength="30"
                    required
                    minlength="3">
            <span class="input-group-btn">
                <button class="btn btn-primary">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>

        <div class="d-none d-sm-block col-sm-4 col-md-6">
            <p class="text-right"><strong>{{ __('validation.attributes.total') }}:</strong> {{ $total }}</p>
        </div>

    </div>
</form>

@if(Request::has('search') && ! empty(Request::get('search')))
    <div class="row">
        <div class="col-xs-12">
            <p class="pl-3">
                <a href="{{ $route }}" class="text-danger">
                    X
                </a>
                <strong>{{ __('validation.attributes.filter_by') }}:</strong>
                {{ Request::get('search') }}
            </p>
        </div>
    </div>
@endif