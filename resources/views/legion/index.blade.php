<?php
/**
 * @var $legions array
 */

$ogp['title'] = "レギオン一覧";
$ogp['description'] = "レギオンの一覧を表示します。現在".count($legions)."のレギオンが登録されています。";
?>
@extends('app.layout', ['title' => 'レギオン一覧'])

@section('head')
    <link rel="stylesheet" href="{{ asset('css/legion.css') }}">
    <style>
        @media screen and (max-width: 500px) and (orientation: portrait){
            #legions{
                display: block;
            }
            .legion{
                width: 100%;
            }
            .more-info{

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
                        <option value="a-grade">格付け順 | 昇順</option>
                        <option value="d-grade">格付け順 | 降順</option>
                        <option value="a-members_count">人数順 | 昇順</option>
                        <option value="d-members_count">人数順 | 降順</option>
                    </select>
                </label>
            </div>
            <div>
                <span class="info">レギオン登録数 : {{ count($legions) }}</span>
            </div>
        </div>
        <div id="legions" class="list two">
            @forelse($legions as $legion_key => $legion)
                <a href="{{ route('legion.show', ['legion' => str_replace('lilyrdf:','',$legion_key)]) }}" class="legion">
                    <div class="legion-info">
                        <div class="legion-grade">{{ $legion['lily:legionGrade'][0] ?? '-' }}</div>
                        <div class="legion-name">
                            <div class="legion-name-en">{{ $legion['schema:name@en'][0] ?? 'LEGION NAME' }}</div>
                            <div class="legion-name-ja">
                                {{ $legion['schema:name'][0] }}
                                {{ !empty($legion['schema:alternateName']) ? ' ('.$legion['schema:alternateName'][0].')' : '' }}
                            </div>
                        </div>
                    </div>
                    <div>
                        所属数 : {!! e($legion['lily:numberOfMembers'][0] ?? '') ?: '<span style="color:gray;">N/A</span>' !!}
                        @if(!empty($legion['rdf:type'][0]) and $legion['rdf:type'][0] == 'lily:Taskforce')
                            <span class="more-info">臨時・特別部隊</span>
                        @endif
                        @if(!empty($legion['lily:disbanded']) and $legion['lily:disbanded'][0] == 'true')
                            <span class="more-info">解散済み</span>
                        @endif
                    </div>
                </a>
            @empty
            @endforelse
        </div>
        <?php if (config('app.debug')) dump($legions) ?>
    </main>
@endsection
