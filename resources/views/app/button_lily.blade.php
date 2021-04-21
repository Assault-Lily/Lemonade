<?php
/**
 * @var $lily array
 * @var $key string
 * @var $legion array
 */
?><a class="list-item-a" href="{{ route('lily.show',['lily' => str_replace('lilyrdf:','',$key)]) }}" title="{{ $lily['schema:name'][0] }}">
    <div class="list-item-image">
        @if(!empty($additional['key']) && !empty($lily[$additional['key']]))
            <div class="additional">{{ implode(',',$lily[$additional['key']]).($additional['suffix'] ?? '') }}</div>
        @endif
        <div style="color: {{ empty($lily['lily:color'][0]) ? 'transparent' : '#'.$lily['lily:color'][0] }}; font-weight: bold; text-align: right;font-size: 13px;">{{ $lily['lily:color'][0] ?? '' }}</div>
    </div>
    <div class="list-item-data">
        <div class="title-ruby">{!! e($lily['lily:nameKana'][0] ?? '') ?: "<i style=\"color:gray\">読みデータなし</i>" !!}</div>
        <div class="title">{{ $lily['schema:name'][0] }}</div>
        <div>
            {{ $lily['lily:garden'][0] ?? 'ガーデン情報なし' }}
            {{ !empty($lily['lily:grade'][0]) ? $lily['lily:grade'][0].'年' : '' }}
            | {{ $legion['schema:name'][0] ?? 'レギオン情報なし' }}
            {{ !empty($legion['schema:alternateName'][0]) ? '('.$legion['schema:alternateName'][0].')' : '' }}
        </div>
        <div>レアスキル : {!! e($lily['lily:rareSkill'][0] ?? '') ?: "<span style=\"color:gray\">N/A</span>" !!}</div>
    </div>
</a>
