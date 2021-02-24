<?php
/**
 * @var $lily \App\Models\Lily
 */
?><a class="list-item-a" href="{{ route('lily.show',['lily' => $lily->slug]) }}" title="{{ $lily->name }}">
    <div class="list-item-image">
        <div style="color: {{ $lily->color ?: 'transparent' }}; font-weight: bold; text-align: right;font-size: 13px;">{{ $lily->color ?: '' }}</div>
    </div>
    <div class="list-item-data">
        <div class="title-ruby">{{ $lily->name_y }}</div>
        <div class="title">{{ $lily->name }}</div>
        <div>
            {{ $triple['garden.name'] ?? 'ガーデン情報なし' }}
            {{ !empty($triple['garden.grade']) ? $triple['garden.grade'].'年' : '' }}
            | {{ $triple['legion.name'] ?? 'レギオン情報なし' }}
            {{-- $triple['legion.position'] ?? '' --}}
        </div>
        <div>レアスキル : {{ $triple['rareSkill'] ?? 'N/A' }}</div>
    </div>
</a>
