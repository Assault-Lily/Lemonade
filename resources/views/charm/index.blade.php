@extends('app.layout', ['title' => 'CHARM一覧'])

@section('head')
    <style>
        .list-item-b{
            width: calc(50% - 5px);
        }

        @media screen and (max-width:500px) and (orientation:portrait){
            .list-item-b{
                width: 100%;
            }
        }

        #sort-select > option{
            text-align: right;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('#sort-select > option').forEach((item)=>{
                if(item.attributes.value.value === "{{ $sortKey }}")
                    item.setAttribute('selected', true);
            });
            document.getElementById('sort-select').addEventListener('change',(e)=>{
                let order = e.target.value.substr(0,1);
                switch (order){
                    case 'a':
                        order = 'asc'; break;
                    case 'd':
                        order = 'desc'; break;
                    default:
                        order = false;
                }
                let sort = e.target.value.substr(2);
                let url = new URL(location.href);
                url.searchParams.set('sort',sort);
                if(order !== false) url.searchParams.set('order',order);
                location.href = url.toString();
                document.body.classList.add('hide');
            });
        });
    </script>
@endsection

@section('main')
    <main>
        <div class="top-options">
            <div>
                <label>
                    <select id="sort-select">
                        <option value="a-name">名前順 | 昇順</option>
                        <option value="d-name">名前順 | 降順</option>
                        <option value="a-name_en">ABC順 | 昇順</option>
                        <option value="d-name_en">ABC順 | 降順</option>
                        <option value="a-manufacturer">メーカー順 | 昇順</option>
                        <option value="d-manufacturer">メーカー順 | 降順</option>
                        <option value="a-product_no">機体番号順 | 昇順</option>
                        <option value="d-product_no">機体番号順 | 降順</option>
                    </select>
                </label>
            </div>
            <div><span class="info">登録機種数 : {{ count($charms) }}機種</span></div>
        </div>
        <div class="list two">
            @forelse($charms as $charmKey => $charm)
                <a href="{{ route('charm.show', ['charm' => removePrefix($charmKey)]) }}" class="list-item-b">
                    <div class="title">
                        <div style="font-size: small" class="lang-en">{{ ($charm['schema:productID'][0] ?? '').' '.$charm['schema:name@en'][0] }}</div>
                        {{ $charm['schema:name'][0] }}
                    </div>
                    <div>{!! e($corporation[$charm['schema:manufacturer'][0] ?? '-']['schema:name'][0] ?? '') ?: '<span style="color:gray">メーカー情報なし</span>' !!}</div>
                </a>
            @empty
            @endforelse
        </div>
        @if(config('app.debug')) @dump($charms, $corporation) @endif
    </main>
@endsection
