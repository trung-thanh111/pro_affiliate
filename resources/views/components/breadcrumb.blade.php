@props(['breadcrumb'])
<div class="page-breadcrumb background">      
        <ul class="uk-list uk-clearfix uk-flex uk-flex-middle">
            <li>
                <a href="{{ route('home.index') }}">Trang chủ</a>
            </li>
            <li>
                <span class="slash">/</span>
            </li>
            @if(!is_null($breadcrumb))
                @foreach($breadcrumb as $key => $val)
                    @php
                        $name = $val->languages->first()->pivot->name;
                        $canonical = write_url($val->languages->first()->pivot->canonical, true, true);
                    @endphp
                    <li>
                        <a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a>
                    </li>
                @endforeach
            @endif
        </ul>
</div>