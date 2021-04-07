@extends('app.layout', ['title' => 'レギオン一覧'])

<?php
/**
 * @var $legions Array
 */
?>

@section('head')
    <link rel="stylesheet" href="{{ asset('css/legion.css') }}">
@endsection

@section('main')
    <main>
        <div class="top-options">
            <div>
                ならべかえ : リソースキー順
            </div>
            <div>
                <span class="info">レギオン登録数 : {{ count($legions) }}</span>
            </div>
        </div>
        <div id="legions" class="list">
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
                        @if(!empty($legion['lily:disbanded']) and $legion['lily:disbanded'][0] == 'true')
                            <div class="more-info">解散済み</div>
                        @endif
                    </div>
                    <div>
                        所属数 : {!! e($legion['lily:numberOfMembers'][0] ?? '') ?: '<span style="color:gray;">N/A</span>' !!}
                    </div>
                </a>
            @empty
            @endforelse
        </div>
        <?php if (config('app.debug')) dump($legions) ?>
    </main>
@endsection
