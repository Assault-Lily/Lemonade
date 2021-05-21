<?php
/**
 * @var $lily array
 * @var $key string
 * @var $legion array
 * @var $icons array
 * @var $icon App\Models\Image
 */
$icon = !empty($icons) ? $icons[array_rand($icons)] : null;
?><a class="list-item-a" href="{{ route('lily.show',['lily' => str_replace('lilyrdf:','',$key)]) }}" title="{{ $lily['schema:name'][0] }}">
    <div class="list-item-image">
        @if(!empty($additional['key']) && !empty($lily[$additional['key']]))
            <div class="additional">{{ implode(', ',$lily[$additional['key']]).($additional['suffix'] ?? '') }}</div>
        @endif
        @if(!empty($icon))
            <img src="{{ $icon->image_url }}" alt="icon">
            @else
            <div class="no-image">NoImage</div>
        @endif
        @if(!empty($lily['lily:color'][0]))
            <div style="color: {{ '#'.$lily['lily:color'][0] }}; font-weight: bold; text-align: right;font-size: 13px;">
                {{ $lily['lily:color'][0] }}
            </div>
        @endif
        @if(!empty($lily['rdf:type'][0]) and $lily['rdf:type'][0] === 'lily:Teacher')
            <div style="color: darkblue; font-weight: bold; text-align: center;font-size: 13px;">
                教導官
            </div>
        @endif
    </div>
    <div class="list-item-data">
        <div class="title-ruby">{!! e($lily['lily:nameKana'][0] ?? '') ?: "<i style=\"color:gray\">読みデータなし</i>" !!}</div>
        <div class="title">{{ $lily['schema:name'][0] }}</div>
        <div>
            {{ $lily['lily:garden'][0] ?? 'ガーデン情報なし' }}
            {{ !empty($lily['lily:grade'][0]) ? convertGradeString($lily['lily:grade'][0]) : '' }}<br>
            {{ !empty($legion['schema:name'][0]) ? 'LG '.$legion['schema:name'][0] : 'レギオン情報なし' }}
            {{ ($lily['lily:legionJobTitle'][0]) ?? '' }}
            {{ !empty($legion['schema:alternateName'][0]) ? '('.$legion['schema:alternateName'][0].')' : '' }}
        </div>
        <div>レアスキル : {!! e($lily['lily:rareSkill'][0] ?? '') ?: "<span style=\"color:gray\">N/A</span>" !!}</div>
    </div>
</a>
