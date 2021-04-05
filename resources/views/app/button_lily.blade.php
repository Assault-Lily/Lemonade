<?php
/**
 * @var $lily \App\Models\Lily
 */
?><a class="list-item-a" href="{{ route('lily.show',['lily' => $lily->slug]) }}" title="{{ $lily->name }}">
    <div class="list-item-image">
        <div style="color: {{ empty($triple['color']) ? 'transparent' : '#'.$triple['color'] }}; font-weight: bold; text-align: right;font-size: 13px;">{{ $triple['color'] ?? '' }}</div>
    </div>
    <div class="list-item-data">
        <div class="title-ruby">{!! e($lily->name_y) ?: "<i style=\"color:gray\">読みデータなし</i>" !!}</div>
        <div class="title">{{ $lily->name }}</div>
        <div>
            {{ $triple['garden'] ?? 'ガーデン情報なし' }}
            {{ !empty($triple['grade']) ? $triple['grade'].'年' : '' }}
            | {{ $triple['legion'] ?? 'レギオン情報なし' }}
            {{ !empty($triple['legionAlternate']) ? '('.$triple['legionAlternate'].')' : '' }}
        </div>
        <div>レアスキル : {!! e($triple['rareSkill'] ?? '') ?: "<span style=\"color:gray\">N/A</span>" !!}</div>
    </div>
</a>
